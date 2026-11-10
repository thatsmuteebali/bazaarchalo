<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_sidebar_and_product_datatable_are_available_without_exposing_numeric_ids(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Product Owner']);
        $shop = $seller->shops()->create(['name' => 'Product Shop', 'status' => 'active']);
        $category = Category::create(['name' => 'Product Category', 'status' => 'active']);
        $product = $this->createProduct($seller, $shop->id, $category->id);
        $collection = Collection::create([
            'seller_id' => $seller->id,
            'name' => 'Product Collection',
            'slug' => 'product-collection',
        ]);
        $product->update(['collection_id' => $collection->id]);

        $listing = $this->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('id="product-table"', false);
        preg_match_all('/<th(?:\s|>)/', $listing->getContent(), $headers);
        $this->assertCount(12, $headers[0]);
        $this->assertStringNotContainsString('<th>Featured</th>', $listing->getContent());

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.products.index'), false);

        $response = $this->get(route('admin.products.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', '<div class="table-user-name">'.$product->name.'</div>')
            ->assertJsonPath('data.0.seller_name', 'Product Owner')
            ->assertJsonPath('data.0.collection_name', 'Product Collection')
            ->assertJsonPath('data.0.status', '<span class="badge-table pending">Pending</span>');
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        $this->assertArrayNotHasKey('is_featured', $response->json('data.0'));
    }

    public function test_admin_can_only_manage_product_status_and_featured_visibility(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $shop = $seller->shops()->create(['name' => 'Admin Product Shop', 'status' => 'active']);
        $category = Category::create(['name' => 'Admin Product Category', 'status' => 'active']);
        $product = $this->createProduct($seller, $shop->id, $category->id);

        $this->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertSee('Product Details')
            ->assertSee($product->slug);

        $this->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('name="status"', false)
            ->assertSee('name="is_featured"', false)
            ->assertSee('value="'.$product->name.'" readonly', false)
            ->assertSee('value="'.$product->slug.'" readonly', false);

        $this->put(route('admin.products.update', $product), [
            'status' => 'active',
            'is_featured' => '1',
            'name' => 'Should Not Change',
            'seller_id' => 999,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Admin Product',
            'seller_id' => $seller->id,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->put(route('admin.products.update', $product), [
            'status' => 'invalid',
            'is_featured' => 'invalid',
        ])->assertSessionHasErrors(['status', 'is_featured']);
    }

    private function createProduct(User $seller, int $shopId, int $categoryId): Product
    {
        return Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shopId,
            'category_id' => $categoryId,
            'name' => 'Admin Product',
            'slug' => 'admin-product-'.$seller->id,
            'short_description' => 'Product summary.',
            'price' => 20,
            'stock' => 2,
            'status' => 'pending',
        ]);
    }
}
