<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'seller']));
    }

    public function test_seller_can_create_view_update_and_delete_a_shop(): void
    {
        Storage::fake('public');

        $this->post(route('seller.shops.store'), [
            'name' => '  Green Market  ',
            'description' => 'Local produce and pantry goods.',
            'address' => '12 Market Street',
            'slug' => 'green-market-custom',
            'banner' => UploadedFile::fake()->image('banner.jpg'),
        ])->assertRedirect(route('seller.shops.index'));

        $shop = Shop::where('name', 'Green Market')->firstOrFail();
        $this->assertSame(auth()->id(), $shop->seller_id);
        $this->assertSame('green-market-custom', $shop->slug);
        $this->assertSame('pending', $shop->status);
        $originalBanner = $shop->banner;
        Storage::disk('public')->assertExists($originalBanner);

        $this->get(route('seller.shops.show', $shop))
            ->assertOk()
            ->assertSee('Green Market');

        $this->put(route('seller.shops.update', $shop), [
            'name' => 'Green Market Online',
            'description' => 'Updated description.',
            'address' => '14 Market Street',
            'slug' => 'green-market-online-custom',
            'banner' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('seller.shops.index'));

        $shop->refresh();
        Storage::disk('public')->assertMissing($originalBanner);
        Storage::disk('public')->assertExists($shop->banner);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Green Market Online',
            'slug' => 'green-market-online-custom',
            'status' => 'pending',
        ]);

        $this->delete(route('seller.shops.destroy', [
            'shop' => $shop,
        ]))->assertRedirect(route('seller.shops.index'));

        Storage::disk('public')->assertExists($shop->banner);
        $this->assertSoftDeleted('shops', ['id' => $shop->id]);

        $this->patch(route('seller.shops.restore', $shop))
            ->assertRedirect(route('seller.shops.index'));
        $this->assertDatabaseHas('shops', ['id' => $shop->id, 'deleted_at' => null]);

        $this->delete(route('seller.shops.destroy', $shop))
            ->assertRedirect(route('seller.shops.index'));
        $this->delete(route('seller.shops.force-delete', $shop))
            ->assertRedirect(route('seller.shops.index'));
        Storage::disk('public')->assertMissing($shop->banner);
        $this->assertDatabaseMissing('shops', ['id' => $shop->id]);
    }

    public function test_shop_form_uses_manual_slug_and_does_not_allow_seller_to_change_status(): void
    {
        $this->get(route('seller.shops.create'))
            ->assertOk()
            ->assertSee('name="slug"', false)
            ->assertDontSee('name="status"', false)
            ->assertDontSee('readonly', false);

        $this->get(route('seller.shops.edit', auth()->user()->shops()->create([
            'name' => 'Existing Shop',
            'slug' => 'existing-shop',
            'status' => 'active',
        ])))
            ->assertOk()
            ->assertSee('value="existing-shop"', false)
            ->assertDontSee('name="status"', false);
    }

    public function test_seller_cannot_access_another_sellers_shop(): void
    {
        $anotherSeller = User::factory()->create(['role' => 'seller']);
        $otherShop = $anotherSeller->shops()->create([
            'name' => 'Private Shop',
            'status' => 'active',
        ]);

        $this->get(route('seller.shops.index'))
            ->assertOk()
            ->assertDontSee('Private Shop');

        $this->get(route('seller.shops.show', $otherShop))->assertNotFound();
        $this->put(route('seller.shops.update', $otherShop), [
            'name' => 'Changed Shop',
            'status' => 'active',
        ])->assertNotFound();
        $this->delete(route('seller.shops.destroy', $otherShop))->assertNotFound();
        $this->patch(route('seller.shops.restore', $otherShop))->assertNotFound();
        $this->delete(route('seller.shops.force-delete', $otherShop))->assertNotFound();

        $this->assertDatabaseHas('shops', ['id' => $otherShop->id, 'name' => 'Private Shop']);
    }

    public function test_shop_routes_and_datatable_do_not_expose_the_database_id(): void
    {
        $shop = auth()->user()->shops()->create([
            'name' => 'Encrypted Route Shop',
            'status' => 'active',
        ]);
        $showUrl = route('seller.shops.show', $shop);

        $this->assertStringNotContainsString('/'.$shop->id, $showUrl);
        $this->get($showUrl)->assertOk()->assertSee('Encrypted Route Shop');
        $this->get('/seller/shops/not-a-valid-encrypted-id')->assertNotFound();

        $response = $this->get(route('seller.shops.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'shops.id', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'banner', 'name' => 'banner', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'name', 'name' => 'shops.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'address', 'name' => 'shops.address', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'shops.status', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        preg_match('#/seller/shops/([^/"]+)#', $response->json('data.0.actions'), $routeMatch);
        $this->assertNotEmpty($routeMatch[1] ?? null);
        $this->assertNotSame((string) $shop->id, $routeMatch[1]);
    }

    public function test_shop_table_filters_status_and_trashed_shops(): void
    {
        $seller = auth()->user();
        $activeShop = $seller->shops()->create(['name' => 'Active Shop', 'status' => 'active']);
        $pendingShop = $seller->shops()->create(['name' => 'Pending Shop', 'status' => 'pending']);
        $trashedShop = $seller->shops()->create(['name' => 'Trashed Shop', 'status' => 'active']);
        $trashedShop->delete();

        $baseRequest = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'shops.id', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'banner', 'name' => 'banner', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'name', 'name' => 'shops.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'address', 'name' => 'shops.address', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'shops.status', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ];

        $activeResponse = $this->get(route('seller.shops.index', $baseRequest + ['status' => 'active']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $activeResponse->assertJsonPath('recordsTotal', 2)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', $activeShop->name);

        $pendingResponse = $this->get(route('seller.shops.index', $baseRequest + ['status' => 'pending']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $pendingResponse->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', $pendingShop->name);

        $trashedResponse = $this->get(route('seller.shops.index', $baseRequest + ['status' => 'trashed']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $trashedResponse->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $trashedShop->name);
        $this->assertStringContainsString('Restore shop', $trashedResponse->json('data.0.actions'));
    }
}
