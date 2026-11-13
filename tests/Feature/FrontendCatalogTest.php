<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\WebContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendCatalogTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_frontend_uses_slugs_and_shows_featured_collections(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $category = Category::create([
            'name' => 'Fresh Produce',
            'status' => 'active',
            'is_featured' => true,
        ]);
        $shop = $seller->shops()->create([
            'name' => 'Green Market',
            'slug' => 'green-market',
            'status' => 'active',
            'is_feature_shop' => true,
        ]);
        $collection = Collection::create([
            'seller_id' => $seller->id,
            'name' => 'Seasonal Picks',
            'slug' => 'seasonal-picks',
            'status' => 'active',
            'is_featured' => true,
        ]);
        $product = $this->createProduct($seller, $category, $shop, $collection, 'Fresh Apples', 'fresh-apples');
        $this->createProduct($seller, $category, $shop, null, 'Other Product', 'other-product');
        $otherCategory = Category::create(['name' => 'Pantry Goods', 'status' => 'active']);
        $otherShop = $seller->shops()->create([
            'name' => 'Pantry Market',
            'slug' => 'pantry-market',
            'status' => 'active',
        ]);
        $this->createProduct($seller, $otherCategory, $otherShop, $collection, 'Mismatched Product', 'mismatched-product');

        $deletedCollection = Collection::create([
            'seller_id' => $seller->id,
            'name' => 'Archived Picks',
            'slug' => 'archived-picks',
            'status' => 'active',
            'is_featured' => true,
        ]);
        $this->createProduct($seller, $category, $shop, $deletedCollection, 'Archived Apples', 'archived-apples');
        $deletedCollection->delete();

        $this->get(route('frontend.home'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee($shop->name)
            ->assertDontSee($otherCategory->name)
            ->assertDontSee($otherShop->name)
            ->assertSee('class="owl-carousel category-carousel"', false)
            ->assertSee('class="owl-carousel collection-carousel"', false)
            ->assertSee(route('frontend.shop', ['collection' => $collection->slug]), false)
            ->assertSee($collection->name)
            ->assertDontSee($deletedCollection->name)
            ->assertDontSee('Archived Apples');

        $this->get('/shop?category%5B%5D=fresh-produce&shop%5B%5D=green-market&collection%5B%5D=seasonal-picks')
            ->assertOk()
            ->assertSee('Fresh Apples')
            ->assertDontSee('Other Product')
            ->assertDontSee('Mismatched Product')
            ->assertSee('value="fresh-produce"', false)
            ->assertSee('value="green-market"', false)
            ->assertSee('name="collection[]" value="seasonal-picks"', false)
            ->assertSee('checked />', false)
            ->assertSee('/product-detail/fresh-apples', false);

        $this->get(route('frontend.shop', ['collection' => $collection->slug]))
            ->assertOk()
            ->assertSee('Fresh Apples')
            ->assertSee('Mismatched Product');

        $this->get(route('frontend.product-detail', ['product' => $product->slug]))
            ->assertOk()
            ->assertSee(route('frontend.shop', ['category' => $category->slug]), false)
            ->assertSee(route('frontend.shop', ['shop' => $shop->slug]), false);

        $this->get(route('frontend.categories'))
            ->assertOk()
            ->assertSee(route('frontend.shop', ['category' => $category->slug]), false);

        $this->get('/shop-listing?category=fresh-produce')
            ->assertOk()
            ->assertSee('Green Market')
            ->assertDontSee('Pantry Market');
    }

    private function createProduct(
        User $seller,
        Category $category,
        Shop $shop,
        ?Collection $collection,
        string $name,
        string $slug,
    ): Product {
        return Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'collection_id' => $collection?->id,
            'name' => $name,
            'slug' => $slug,
            'price' => 100,
            'stock' => 5,
            'status' => 'active',
        ]);
    }
}
