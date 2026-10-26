<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_view_only_their_items_and_accept_or_reject_pending_items(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $outsider = User::factory()->create(['role' => 'seller']);
        $category = Category::create(['name' => 'Produce', 'status' => 'active']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Seller Shop', 'status' => 'active']);
        $otherShop = Shop::create(['seller_id' => $otherSeller->id, 'name' => 'Other Shop', 'status' => 'active']);
        $productToReject = $this->createProduct($seller, $shop, $category, 'Seasonal Pears', 3, 10);
        $productToAccept = $this->createProduct($seller, $shop, $category, 'Local Plums', 7, 5);

        $order = Order::create([
            'user_id' => $customer->id,
            'customer_name' => 'Customer Name',
            'customer_phone' => '03000000000',
            'delivery_address' => '12 Market Road',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'subtotal' => 55,
            'shipping_total' => 10,
            'total' => 65,
            'payable_total' => 65,
            'status' => Order::STATUS_PENDING,
        ]);
        $order->payments()->create([
            'method' => 'cod',
            'amount' => 65,
            'status' => OrderPayment::STATUS_PENDING,
        ]);
        $rejectItem = $order->items()->create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $productToReject->id,
            'product_name' => $productToReject->name,
            'shop_name' => $shop->name,
            'unit_price' => 10,
            'quantity' => 2,
            'line_total' => 20,
            'status' => OrderItem::STATUS_PENDING,
        ]);
        $acceptItem = $order->items()->create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $productToAccept->id,
            'product_name' => $productToAccept->name,
            'shop_name' => $shop->name,
            'unit_price' => 5,
            'quantity' => 1,
            'line_total' => 5,
            'status' => OrderItem::STATUS_PENDING,
        ]);
        $order->items()->create([
            'seller_id' => $otherSeller->id,
            'shop_id' => $otherShop->id,
            'product_name' => 'Other seller private item',
            'shop_name' => $otherShop->name,
            'unit_price' => 30,
            'quantity' => 1,
            'line_total' => 30,
            'status' => OrderItem::STATUS_PENDING,
        ]);

        $this->actingAs($seller)
            ->get(route('seller.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('3 units')
            ->assertDontSee('Other seller private item');

        $detailUrl = route('seller.orders.show', $order);
        $this->get($detailUrl)
            ->assertOk()
            ->assertSee('Seasonal Pears')
            ->assertSee('Local Plums')
            ->assertDontSee('Other seller private item');

        $this->from($detailUrl)->patch(route('seller.orders.items.decide', $rejectItem), [
            'decision' => OrderItem::STATUS_REJECTED,
            'reason' => 'This item is temporarily unavailable.',
        ])->assertRedirect($detailUrl);

        $this->assertSame(5, $productToReject->fresh()->stock);
        $this->assertSame(45.0, (float) $order->fresh()->payable_total);
        $this->assertDatabaseHas('order_payments', [
            'order_id' => $order->id,
            'status' => OrderPayment::STATUS_PENDING,
            'amount' => 45,
        ]);
        $this->assertDatabaseHas('order_items', [
            'id' => $rejectItem->id,
            'status' => OrderItem::STATUS_REJECTED,
            'status_reason' => 'This item is temporarily unavailable.',
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $productToReject->id,
            'reason' => 'return',
            'quantity_change' => 2,
        ]);
        $this->assertDatabaseHas('order_tracking', [
            'order_id' => $order->id,
            'order_item_id' => $rejectItem->id,
            'seller_id' => $seller->id,
            'status' => OrderItem::STATUS_REJECTED,
            'is_public' => true,
        ]);

        $this->from($detailUrl)->patch(route('seller.orders.items.decide', $acceptItem), [
            'decision' => OrderItem::STATUS_ACCEPTED,
        ])->assertRedirect($detailUrl);

        $this->assertSame(7, $productToAccept->fresh()->stock);
        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);
        $this->assertDatabaseHas('order_items', [
            'id' => $acceptItem->id,
            'status' => OrderItem::STATUS_ACCEPTED,
        ]);

        $this->from($detailUrl)->patch(route('seller.orders.items.decide', $rejectItem), [
            'decision' => OrderItem::STATUS_ACCEPTED,
        ])->assertSessionHasErrors('decision');
        $this->assertSame(5, $productToReject->fresh()->stock);

        $this->actingAs($outsider)
            ->get($detailUrl)
            ->assertNotFound();
        $this->patch(route('seller.orders.items.decide', $acceptItem), [
            'decision' => OrderItem::STATUS_ACCEPTED,
        ])->assertNotFound();
    }

    public function test_rejecting_the_only_order_item_cancels_the_order_and_clears_its_payable_balance(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $category = Category::create(['name' => 'Bakery', 'status' => 'active']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Corner Bakery', 'status' => 'active']);
        $product = $this->createProduct($seller, $shop, $category, 'Sourdough Loaf', 1, 10);
        $order = Order::create([
            'user_id' => $customer->id,
            'customer_name' => 'Customer Name',
            'customer_phone' => '03000000000',
            'delivery_address' => '12 Market Road',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'subtotal' => 10,
            'shipping_total' => 5,
            'total' => 15,
            'payable_total' => 15,
            'status' => Order::STATUS_PENDING,
        ]);
        $item = $order->items()->create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 10,
            'quantity' => 1,
            'line_total' => 10,
            'status' => OrderItem::STATUS_PENDING,
        ]);

        $this->actingAs($seller)
            ->patch(route('seller.orders.items.decide', $item), [
                'decision' => OrderItem::STATUS_REJECTED,
                'reason' => 'Sold out.',
            ])
            ->assertRedirect(route('seller.orders.show', $order));

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(0.0, (float) $order->fresh()->payable_total);
        $this->assertSame(2, $product->fresh()->stock);
    }

    private function createProduct(User $seller, Shop $shop, Category $category, string $name, int $stock, float $price): Product
    {
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
