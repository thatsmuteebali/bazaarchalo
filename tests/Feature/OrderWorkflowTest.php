<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
    }

    public function test_order_notifications_decisions_rider_delivery_and_product_review_work_together(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Order Customer']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'Active']);
        $secondAdmin = User::factory()->create(['role' => 'admin', 'status' => 'Active']);
        $inactiveAdmin = User::factory()->create(['role' => 'admin', 'status' => 'Inactive']);
        $appleSeller = User::factory()->create(['role' => 'seller', 'status' => 'Active']);
        $pearSeller = User::factory()->create(['role' => 'seller', 'status' => 'Active']);
        $inactiveSeller = User::factory()->create(['role' => 'seller', 'status' => 'Inactive']);
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $otherRider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $inactiveRider = User::factory()->create(['role' => 'rider', 'status' => 'Inactive']);
        $category = Category::create(['name' => 'Produce', 'status' => 'active']);
        $appleShop = Shop::create([
            'seller_id' => $appleSeller->id,
            'name' => 'Apple Shop',
            'status' => 'active',
            'is_feature_shop' => true,
        ]);
        $pearShop = Shop::create(['seller_id' => $pearSeller->id, 'name' => 'Pear Shop', 'status' => 'active']);
        $inactiveShop = Shop::create(['seller_id' => $inactiveSeller->id, 'name' => 'Inactive Shop', 'status' => 'active']);
        $apples = $this->createProduct($appleSeller, $appleShop, $category, 'Fresh Apples', 'fresh-apples');
        $pears = $this->createProduct($pearSeller, $pearShop, $category, 'Ripe Pears', 'ripe-pears');
        $berries = $this->createProduct($inactiveSeller, $inactiveShop, $category, 'Wild Berries', 'wild-berries');
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash on Delivery',
            'slug' => 'cod',
            'status' => 'active',
        ]);

        $this->actingAs($customer)->withSession([
            'cart_lines' => [
                'p'.$apples->id => ['product_id' => $apples->id, 'variant_id' => null, 'qty' => 1],
                'p'.$pears->id => ['product_id' => $pears->id, 'variant_id' => null, 'qty' => 1],
                'p'.$berries->id => ['product_id' => $berries->id, 'variant_id' => null, 'qty' => 1],
            ],
        ])->post(route('customer.checkout.place'), [
            'name' => $customer->name,
            'phone' => '03000000000',
            'address' => '12 Market Road',
            'payment_method' => $paymentMethod->id,
            'terms' => '1',
        ]);

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();
        $appleItem = $order->items()->where('product_id', $apples->id)->firstOrFail();
        $pearItem = $order->items()->where('product_id', $pears->id)->firstOrFail();
        $berryItem = $order->items()->where('product_id', $berries->id)->firstOrFail();

        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(1, $secondAdmin->notifications()->count());
        $this->assertSame('New order received', $admin->notifications()->first()->data['title']);
        $this->assertSame(0, $inactiveAdmin->notifications()->count());
        $this->assertSame(1, $appleSeller->notifications()->count());
        $this->assertSame(1, $pearSeller->notifications()->count());
        $this->assertSame(0, $inactiveSeller->notifications()->count());

        $this->actingAs($admin)
            ->post(route('admin.orders.assign-rider', $order), ['rider_id' => $rider->id])
            ->assertSessionHasErrors('rider_id');
        $this->assertNull($order->fresh()->rider_id);

        $this->patch(route('admin.orders.items.decide', [$order, $appleItem]), [
            'decision' => OrderItem::STATUS_ACCEPTED,
        ])->assertRedirect(route('admin.orders.show', $order));
        $this->assertHasNotification($appleSeller, 'Order item accepted');
        $this->assertHasNotification($customer, 'Order item accepted');

        $this->actingAs($pearSeller)
            ->patch(route('seller.orders.items.decide', $pearItem), [
                'decision' => OrderItem::STATUS_REJECTED,
                'reason' => 'This item is not available.',
            ])
            ->assertRedirect(route('seller.orders.show', $order));
        $this->assertHasNotification($admin, 'Seller rejected an order item');
        $this->assertHasNotification($secondAdmin, 'Seller rejected an order item');
        $this->assertHasNotification($customer, 'Order item rejected');
        $this->assertSame(0, $inactiveAdmin->notifications()->count());

        $this->actingAs($customer)
            ->post(route('customer.orders.items.review', [$order, $appleItem]), [
                'rating' => 6,
                'description' => 'An invalid rating should be rejected.',
            ])
            ->assertSessionHasErrors('rating');

        $this->actingAs($admin)
            ->patch(route('admin.orders.items.decide', [$order, $berryItem]), [
                'decision' => OrderItem::STATUS_REJECTED,
                'reason' => 'This seller is currently unavailable.',
            ])
            ->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(1, $inactiveSeller->notifications()->count());
        $this->assertHasNotification($inactiveSeller, 'Order item rejected');

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Assign rider')
            ->assertSee($rider->name)
            ->assertSee($otherRider->name)
            ->assertDontSee($inactiveRider->name);

        $this->post(route('admin.orders.assign-rider', $order), ['rider_id' => $inactiveRider->id])
            ->assertSessionHasErrors('rider_id');
        $this->post(route('admin.orders.assign-rider', $order), ['rider_id' => $rider->id])
            ->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame($rider->id, $order->fresh()->rider_id);
        $this->assertHasNotification($appleSeller, 'Rider assigned to your order');
        $this->assertHasNotification($customer, 'Your order is ready for delivery');
        $this->assertSame('New delivery assigned', $rider->notifications()->first()->data['title']);
        $this->assertSame(1, $pearSeller->notifications()->count());

        $this->actingAs($otherRider)
            ->get(route('rider.orders.show', $order))
            ->assertNotFound();

        $this->actingAs($rider)
            ->get(route('rider.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->get(route('rider.orders.show', $order))
            ->assertOk()
            ->assertSee('Fresh Apples')
            ->assertDontSee('Ripe Pears')
            ->assertSee('12 Market Road');

        $this->patch(route('rider.orders.items.status', [$order, $appleItem]), [
            'status' => OrderItem::STATUS_SHIPPED,
        ])->assertRedirect(route('rider.orders.show', $order));
        $this->assertSame(OrderItem::STATUS_SHIPPED, $appleItem->fresh()->status);
        $this->assertHasNotification($customer, 'Order item picked up');

        $this->patch(route('rider.orders.items.status', [$order, $appleItem]), [
            'status' => OrderItem::STATUS_DELIVERED,
        ])->assertRedirect(route('rider.orders.show', $order));
        $this->assertSame(OrderItem::STATUS_DELIVERED, $appleItem->fresh()->status);
        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
        $this->assertHasNotification($customer, 'Order item delivered');

        $this->actingAs($customer)
            ->get(route('customer.order-detail', ['order' => $order->order_number]))
            ->assertOk()
            ->assertSee('Review Fresh Apples')
            ->assertSee('Ripe Pears');

        $reviewUrl = route('customer.orders.items.review', [$order, $appleItem]);
        $this->post($reviewUrl, [
            'rating' => 5,
            'description' => 'The apples were fresh and exactly as described.',
        ])->assertRedirect(route('customer.order-detail', ['order' => $order->order_number]));

        $this->assertDatabaseHas('product_reviews', [
            'order_item_id' => $appleItem->id,
            'product_id' => $apples->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'description' => 'The apples were fresh and exactly as described.',
        ]);

        $this->get(route('frontend.shop'))
            ->assertOk()
            ->assertSee('5.0 (1)');

        $this->get(route('frontend.home'))
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('(1 review)');
        $this->get(route('frontend.shop-listing'))
            ->assertOk()
            ->assertSee('5.0')
            ->assertSee('(1 review)');

        $this->post($reviewUrl, [
            'rating' => 4,
            'description' => 'A second review should not be accepted.',
        ])->assertSessionHasErrors('review');

        $this->get(route('frontend.product-detail', ['product' => $apples->slug]))
            ->assertOk()
            ->assertSee('Reviews (1)')
            ->assertSee('The apples were fresh and exactly as described.');
    }

    private function createProduct(
        User $seller,
        Shop $shop,
        Category $category,
        string $name,
        string $slug,
    ): Product {
        return Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'price' => 25,
            'stock' => 5,
            'status' => 'active',
            'has_variants' => false,
        ]);
    }

    private function assertHasNotification(User $user, string $title): void
    {
        $this->assertTrue(
            $user->notifications()->get()->contains(
                fn ($notification) => ($notification->data['title'] ?? null) === $title,
            ),
            'Expected '.$user->name.' to receive the "'.$title.'" notification.',
        );
    }
}
