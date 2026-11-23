<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\WebContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'Website Favicon',
            'Website Address',
            'Website Email',
            'Website Facebook Url',
            'Website Instagram Url',
            'Website Tiktok Url',
            'Website Header Logo',
            'Website Footer Logo',
            'Website Phone Number',
        ] as $field) {
            WebContent::create([
                'page' => 'setting',
                'field' => $field,
                'value' => '',
                'type' => 'text',
            ]);
        }

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_list_orders_view_tracking_and_decide_items(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Order Customer']);
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Order Seller']);
        $category = Category::create(['name' => 'Produce', 'status' => 'active']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Seller Shop', 'status' => 'active']);
        $acceptedProduct = $this->createProduct($seller, $shop, $category, 'Fresh Apples', 4, 10);
        $rejectedProduct = $this->createProduct($seller, $shop, $category, 'Ripe Pears', 2, 20);
        $order = Order::create([
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => '03000000000',
            'customer_email' => $customer->email,
            'delivery_address' => '12 Market Road',
            'payment_method' => Order::METHOD_COD,
            'payment_status' => Order::PAYMENT_UNPAID,
            'subtotal' => 50,
            'shipping_total' => 10,
            'total' => 60,
            'payable_total' => 60,
            'status' => Order::STATUS_PENDING,
        ]);
        $payment = $order->payments()->create([
            'method' => Order::METHOD_COD,
            'amount' => 60,
            'status' => OrderPayment::STATUS_PENDING,
        ]);
        $acceptItem = $order->items()->create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $acceptedProduct->id,
            'product_name' => $acceptedProduct->name,
            'shop_name' => $shop->name,
            'unit_price' => 10,
            'quantity' => 1,
            'line_total' => 10,
            'status' => OrderItem::STATUS_PENDING,
        ]);
        $rejectItem = $order->items()->create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $rejectedProduct->id,
            'product_name' => $rejectedProduct->name,
            'shop_name' => $shop->name,
            'unit_price' => 20,
            'quantity' => 2,
            'line_total' => 40,
            'status' => OrderItem::STATUS_PENDING,
        ]);
        $order->tracking()->create([
            'status' => 'placed',
            'title' => 'Order received',
            'is_public' => true,
            'created_by' => $customer->id,
        ]);

        $this->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('id="order-table"', false)
            ->assertSee(route('admin.orders.index'), false);

        $listResponse = $this->get(route('admin.orders.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_UNPAID,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $listResponse->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.customer', '<div class="table-user-name">Order Customer</div><div class="table-user-sub">03000000000</div>');
        $this->assertStringContainsString($order->order_number, $listResponse->json('data.0.order'));
        $this->assertArrayNotHasKey('id', $listResponse->json('data.0'));

        $detailUrl = route('admin.orders.show', $order);
        $this->get($detailUrl)
            ->assertOk()
            ->assertSee('Fresh Apples')
            ->assertSee('Ripe Pears')
            ->assertSee('Order received')
            ->assertSee('Accept item')
            ->assertSee('Add tracking update');

        $this->patch(route('admin.orders.items.decide', [$order, $acceptItem]), [
            'decision' => OrderItem::STATUS_ACCEPTED,
        ])->assertRedirect($detailUrl);

        $this->assertDatabaseHas('order_items', [
            'id' => $acceptItem->id,
            'status' => OrderItem::STATUS_ACCEPTED,
        ]);
        $this->assertDatabaseHas('order_tracking', [
            'order_id' => $order->id,
            'order_item_id' => $acceptItem->id,
            'seller_id' => $seller->id,
            'status' => OrderItem::STATUS_ACCEPTED,
            'title' => 'Admin accepted an item',
            'created_by' => $this->admin->id,
        ]);

        $reason = 'The pear stock is damaged.';
        $this->patch(route('admin.orders.items.decide', [$order, $rejectItem]), [
            'decision' => OrderItem::STATUS_REJECTED,
            'reason' => $reason,
        ])->assertRedirect($detailUrl);

        $this->assertSame(4, $rejectedProduct->fresh()->stock);
        $this->assertSame(20.0, (float) $order->fresh()->payable_total);
        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);
        $this->assertSame(20.0, (float) $payment->fresh()->amount);
        $this->assertDatabaseHas('order_items', [
            'id' => $rejectItem->id,
            'status' => OrderItem::STATUS_REJECTED,
            'status_reason' => $reason,
        ]);

        $this->post(route('admin.orders.tracking.store', $order), [
            'order_item_id' => $acceptItem->id,
            'status' => 'shipped',
            'title' => 'Package handed to courier',
            'description' => 'The courier collected the package.',
            'courier_name' => 'Local Express',
            'tracking_number' => 'LE-12345',
            'location' => 'Central depot',
            'is_public' => '1',
        ])->assertRedirect($detailUrl);

        $this->assertDatabaseHas('order_tracking', [
            'order_id' => $order->id,
            'order_item_id' => $acceptItem->id,
            'seller_id' => $seller->id,
            'status' => 'shipped',
            'title' => 'Package handed to courier',
            'courier_name' => 'Local Express',
            'tracking_number' => 'LE-12345',
            'location' => 'Central depot',
            'is_public' => true,
            'created_by' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('order_items', [
            'id' => $acceptItem->id,
            'status' => OrderItem::STATUS_SHIPPED,
            'courier_name' => 'Local Express',
            'tracking_number' => 'LE-12345',
        ]);

        $this->post(route('admin.orders.tracking.store', $order), [
            'order_item_id' => $acceptItem->id,
            'status' => 'delivered',
            'title' => 'Package delivered',
            'is_public' => '1',
        ])->assertRedirect($detailUrl);
        $this->assertSame(OrderItem::STATUS_DELIVERED, $acceptItem->fresh()->status);
        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
        $this->assertSame('LE-12345', $acceptItem->fresh()->tracking_number);

        $this->post(route('admin.orders.tracking.store', $order), [
            'status' => 'note',
            'title' => 'Internal dispatch note',
            'description' => 'Customer should not see this note.',
        ])->assertRedirect($detailUrl);

        $this->actingAs($customer)
            ->get(route('customer.order-tracking', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee('Package handed to courier')
            ->assertSee('Package delivered')
            ->assertDontSee('Internal dispatch note')
            ->assertDontSee('Customer should not see this note.');
    }

    private function createProduct(
        User $seller,
        Shop $shop,
        Category $category,
        string $name,
        int $stock,
        float $price,
    ): Product {
        return Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'status' => 'active',
            'has_variants' => false,
        ]);
    }
}
