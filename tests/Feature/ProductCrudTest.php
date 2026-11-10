<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private Category $category;

    private $shop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->actingAs($this->seller);
        $this->category = Category::create(['name' => 'Active Category', 'status' => 'active']);
        $this->shop = $this->seller->shops()->create(['name' => 'Active Shop', 'status' => 'active']);
    }

    public function test_seller_creates_pending_product_with_manual_slug_and_can_soft_delete_restore_and_force_delete(): void
    {
        Storage::fake('public');

        $listing = $this->get(route('seller.products.index'))
            ->assertOk()
            ->assertSee('id="product-table"', false);
        preg_match_all('/<th(?:\s|>)/', $listing->getContent(), $headers);
        $this->assertCount(11, $headers[0]);
        $this->assertStringNotContainsString('<th>Featured</th>', $listing->getContent());
        $this->assertSame(
            1,
            substr_count($listing->getContent(), "{ data: 'slug', name: 'products.slug' }")
        );

        $this->get(route('seller.products.create'))
            ->assertOk()
            ->assertSee('name="slug"', false)
            ->assertDontSee('name="status"', false)
            ->assertDontSee('name="is_featured"', false);

        $this->post(route('seller.products.store'), $this->productInput([
            'status' => 'active',
            'is_featured' => '1',
        ]))->assertRedirect(route('seller.products.index'));

        $product = Product::where('slug', 'manual-product-slug')->firstOrFail();
        $this->assertSame($this->seller->id, $product->seller_id);
        $this->assertSame('pending', $product->status);
        $this->assertFalse($product->is_featured);
        $imagePaths = $product->images()->pluck('path')->all();
        $this->assertCount(2, $imagePaths);
        Storage::disk('public')->assertExists($imagePaths);

        $response = $this->get(route('seller.products.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', '<div class="table-user-name">New Product</div>')
            ->assertJsonPath('data.0.slug', 'manual-product-slug')
            ->assertJsonPath('data.0.status', '<span class="badge-table pending">Pending</span>');
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        $this->get(route('seller.products.show', $product))->assertOk()->assertSee('New Product');

        $this->delete(route('seller.products.destroy', $product))
            ->assertRedirect(route('seller.products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        Storage::disk('public')->assertExists($imagePaths);

        $trashedResponse = $this->get(route('seller.products.index', [
            'draw' => 2,
            'start' => 0,
            'length' => 10,
            'status' => 'trashed',
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $trashedResponse->assertJsonPath('recordsTotal', 1);

        $this->patch(route('seller.products.restore', $product))
            ->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);

        $this->delete(route('seller.products.destroy', $product))
            ->assertRedirect(route('seller.products.index'));
        $this->delete(route('seller.products.force-delete', $product))
            ->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($imagePaths);
    }

    public function test_product_slug_must_be_unique_and_use_lowercase_hyphenated_format(): void
    {
        Product::create([
            'seller_id' => $this->seller->id,
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'name' => 'Existing Product',
            'slug' => 'existing-product',
            'short_description' => 'A product.',
            'price' => 10,
            'stock' => 0,
            'status' => 'pending',
        ]);

        $this->from(route('seller.products.create'))
            ->post(route('seller.products.store'), $this->productInput(['slug' => 'existing-product']))
            ->assertRedirect(route('seller.products.create'))
            ->assertSessionHasErrors('slug');

        $this->from(route('seller.products.create'))
            ->post(route('seller.products.store'), $this->productInput(['slug' => 'Invalid Slug']))
            ->assertRedirect(route('seller.products.create'))
            ->assertSessionHasErrors('slug');
    }

    public function test_seller_product_table_displays_and_sorts_by_collection(): void
    {
        $zCollection = Collection::create([
            'seller_id' => $this->seller->id,
            'name' => 'Z Collection',
            'slug' => 'z-collection',
        ]);
        $aCollection = Collection::create([
            'seller_id' => $this->seller->id,
            'name' => 'A Collection',
            'slug' => 'a-collection',
        ]);

        $zProduct = Product::create([
            'seller_id' => $this->seller->id,
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'collection_id' => $zCollection->id,
            'name' => 'Z Product',
            'slug' => 'z-product',
            'short_description' => 'Product summary.',
            'price' => 10,
            'stock' => 1,
            'status' => 'pending',
        ]);
        $aProduct = Product::create([
            'seller_id' => $this->seller->id,
            'shop_id' => $this->shop->id,
            'category_id' => $this->category->id,
            'collection_id' => $aCollection->id,
            'name' => 'A Product',
            'slug' => 'a-product',
            'short_description' => 'Product summary.',
            'price' => 20,
            'stock' => 2,
            'status' => 'pending',
        ]);

        $response = $this->get(route('seller.products.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [['column' => 4, 'dir' => 'asc']],
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'products.id', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'image', 'name' => 'image', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'name', 'name' => 'products.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'shop_name', 'name' => 'shop.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'collection_name', 'name' => 'collection.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'category_name', 'name' => 'category.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'slug', 'name' => 'products.slug', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'price', 'name' => 'price', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'stock', 'name' => 'products.stock', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'products.status', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'is_featured', 'name' => 'products.is_featured', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('data.0.collection_name', 'A Collection')
            ->assertJsonPath('data.0.name', '<div class="table-user-name">A Product</div>')
            ->assertJsonPath('data.1.collection_name', 'Z Collection');
        $this->assertNotSame($zProduct->id, $aProduct->id);
    }

    private function productInput(array $overrides = []): array
    {
        return array_replace([
            'name' => 'New Product',
            'slug' => 'manual-product-slug',
            'category_id' => $this->category->id,
            'shop_id' => $this->shop->id,
            'short_description' => 'A short description.',
            'description' => 'Product details.',
            'price' => '12.50',
            'stock' => 0,
            'has_variants' => '0',
            'images' => [
                UploadedFile::fake()->image('product-one.jpg'),
                UploadedFile::fake()->image('product-two.jpg'),
            ],
        ], $overrides);
    }
}
