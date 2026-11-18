<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutOrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
    }

    public function test_customer_can_place_an_order_and_view_its_confirmation_and_tracking(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Local Market', 'status' => 'active']);
        $category = Category::create(['name' => 'Fruit', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Fresh Apples',
            'price' => 125,
            'stock' => 5,
            'status' => 'active',
            'has_variants' => false,
        ]);
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash on Delivery',
            'slug' => 'cod',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer)->withSession([
            'cart_lines' => [
                'p' . $product->id => ['product_id' => $product->id, 'variant_id' => null, 'qty' => 2],
            ],
        ])->post(route('customer.checkout.place'), [
            'name' => 'Customer Name',
            'phone' => '03000000000',
            'email' => '',
            'address' => 'House 12, Main Road',
            'payment_method' => $paymentMethod->id,
            'terms' => '1',
        ]);

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();
        $response->assertRedirect(route('customer.order-success', ['order' => $order->order_number]));
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(250.0, (float) $order->subtotal);
        $this->assertSame(250.0, (float) $order->total);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'product_name' => 'Fresh Apples',
            'quantity' => 2,
            'status' => OrderItem::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('order_payments', [
            'order_id' => $order->id,
            'method' => 'cod',
            'status' => OrderPayment::STATUS_PENDING,
            'amount' => 250,
        ]);
        $this->assertDatabaseHas('order_tracking', [
            'order_id' => $order->id,
            'status' => 'placed',
            'title' => 'Order placed',
            'is_public' => true,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'reason' => 'sale',
            'quantity_change' => -2,
        ]);
        $this->assertSame([], $this->app['session.store']->get('cart_lines'));

        $this->get(route('customer.order-success', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Fresh Apples')
            ->assertSee('alt="Fresh Apples"', false);

        $this->get(route('customer.order-tracking', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Order placed')
            ->assertSee('House 12, Main Road')
            ->assertSee('alt="Fresh Apples"', false);

        $this->get(route('customer.orders'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('2 items');

        $this->get(route('customer.order-detail', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Cash on Delivery')
            ->assertSee('Fresh Apples')
            ->assertSee('House 12, Main Road')
            ->assertSee('alt="Fresh Apples"', false);

        $anotherCustomer = User::factory()->create(['role' => 'customer']);
        $anotherOrder = Order::create([
            'user_id' => $anotherCustomer->id,
            'customer_name' => 'Another Customer',
            'customer_phone' => '03000000001',
            'delivery_address' => 'Another address',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'subtotal' => 50,
            'shipping_total' => 0,
            'total' => 50,
            'payable_total' => 50,
            'status' => 'pending',
        ]);
        $this->assertSame('Cash on Delivery', $order->payment_method_label);

        $this->get(route('customer.orders'))
            ->assertDontSee($anotherOrder->order_number);

        $this->get(route('customer.order-detail', ['order' => $anotherOrder->order_number]))
            ->assertRedirect(route('customer.orders'));
    }

    public function test_customer_can_place_an_order_using_a_dynamic_online_method(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Online Shop', 'status' => 'active']);
        $category = Category::create(['name' => 'Fruit', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Fresh Pears',
            'price' => 80,
            'stock' => 3,
            'status' => 'active',
            'has_variants' => false,
        ]);
        $paymentMethod = PaymentMethod::create([
            'name' => 'Custom Wallet',
            'slug' => 'custom-wallet',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer)->withSession([
            'cart_lines' => [
                'p' . $product->id => ['product_id' => $product->id, 'variant_id' => null, 'qty' => 1],
            ],
        ])->post(route('customer.checkout.place'), [
            'name' => 'Customer Name',
            'phone' => '03000000000',
            'address' => 'House 12, Main Road',
            'payment_method' => $paymentMethod->id,
            'terms' => '1',
        ]);

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();
        $response->assertRedirect(route('customer.order-success', ['order' => $order->order_number]));
        $this->assertSame(Order::METHOD_ONLINE, $order->payment_method);
        $this->assertSame('custom-wallet', $order->online_payment_method);
        $this->assertSame('Custom Wallet', $order->payment_method_label);
        $this->assertDatabaseHas('order_payments', [
            'order_id' => $order->id,
            'method' => Order::METHOD_ONLINE,
            'gateway' => 'custom-wallet',
        ]);

        $this->get(route('customer.order-success', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee('Custom Wallet');
    }
}
