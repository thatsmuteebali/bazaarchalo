<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerWishlistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
    }

    public function test_guest_is_redirected_to_login_for_wishlist_routes(): void
    {
        $product = $this->createProduct();

        $this->get(route('customer.wishlist'))
            ->assertRedirect(route('login'));

        $this->post(route('customer.wishlist.toggle', $product))
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_add_view_and_remove_wishlist_products(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'google_id' => 'wishlist-customer',
        ]);
        $product = $this->createProduct();

        $this->actingAs($customer)
            ->post(route('customer.wishlist.toggle', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        $this->get(route('customer.wishlist'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('wishlist-button active')
            ->assertSee('Remove from wishlist');

        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'google_id' => 'other-wishlist-customer',
        ]);
        $this->actingAs($otherCustomer)
            ->get(route('customer.wishlist'))
            ->assertOk()
            ->assertSee('Your wishlist is empty.')
            ->assertDontSee($product->name);

        $this->actingAs($customer);
        $this->post(route('customer.wishlist.toggle', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        $this->get(route('customer.wishlist'))
            ->assertOk()
            ->assertSee('Your wishlist is empty.');
    }

    private function createProduct(): Product
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $category = Category::create([
            'name' => 'Fresh Produce',
            'status' => 'active',
        ]);
        $shop = Shop::create([
            'seller_id' => $seller->id,
            'name' => 'Green Market',
            'slug' => 'green-market-'.$seller->id,
            'status' => 'active',
        ]);

        return Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Fresh Apples',
            'slug' => 'fresh-apples-'.$seller->id,
            'price' => 100,
            'stock' => 5,
            'status' => 'active',
        ]);
    }
}
