<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShopManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_sidebar_links_to_shop_management(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.shops.index'), false);
    }

    public function test_shop_datatable_lists_shops_without_exposing_database_ids(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Shop Owner']);
        $shop = $seller->shops()->create([
            'name' => 'Admin Managed Shop',
            'slug' => 'admin-managed-shop',
            'status' => 'pending',
            'banner' => 'shops/admin-managed.jpg',
            'is_feature_shop' => true,
        ]);

        $response = $this->get(route('admin.shops.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $shop->name)
            ->assertJsonPath('data.0.seller_name', $seller->name)
            ->assertJsonPath('data.0.status', '<span class="badge-table pending">Pending</span>')
            ->assertJsonPath('data.0.is_feature_shop', 'Yes');
        $this->assertStringContainsString('storage/shops/admin-managed.jpg', $response->json('data.0.banner'));
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        $this->assertStringNotContainsString('/admin/shops/'.$shop->id.'/', $response->getContent());
        preg_match('#/admin/shops/([^/"]+)/show#', $response->json('data.0.actions'), $routeMatch);
        $this->assertNotEmpty($routeMatch[1] ?? null);
        $this->assertNotSame((string) $shop->id, $routeMatch[1]);
    }

    public function test_admin_can_view_shop_and_edit_readonly_fields_status_and_featured_flag(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Readonly Owner']);
        $shop = $seller->shops()->create([
            'name' => 'Readonly Shop',
            'slug' => 'readonly-shop',
            'description' => 'Seller entered details.',
            'address' => 'Market Road',
            'status' => 'pending',
            'is_primary' => true,
            'is_feature_shop' => false,
        ]);

        $this->get(route('admin.shops.show', $shop))
            ->assertOk()
            ->assertSee('Readonly Owner')
            ->assertSee('Seller entered details.')
            ->assertSee('Market Road')
            ->assertSee('Primary Shop')
            ->assertSee('Featured Shop');

        $this->get(route('admin.shops.edit', $shop))
            ->assertOk()
            ->assertSee('name="status"', false)
            ->assertSee('name="is_feature_shop"', false)
            ->assertSee('value="Readonly Shop" readonly', false)
            ->assertSee('value="readonly-shop" readonly', false)
            ->assertSee('Seller entered details.')
            ->assertSee('Market Road')
            ->assertSee('Primary Shop');

        $this->put(route('admin.shops.update', $shop), [
            'status' => 'active',
            'is_feature_shop' => '1',
            'name' => 'Should Not Change',
            'seller_id' => 999,
        ])->assertRedirect(route('admin.shops.index'));

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Readonly Shop',
            'seller_id' => $seller->id,
            'status' => 'active',
            'is_feature_shop' => true,
        ]);
    }

    public function test_admin_can_disable_feature_and_invalid_updates_are_rejected(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $shop = $seller->shops()->create([
            'name' => 'Featured Shop',
            'status' => 'active',
            'is_feature_shop' => true,
        ]);

        $this->put(route('admin.shops.update', $shop), [
            'status' => 'inactive',
            'is_feature_shop' => '0',
        ])->assertRedirect(route('admin.shops.index'));

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'status' => 'inactive',
            'is_feature_shop' => false,
        ]);

        $this->put(route('admin.shops.update', $shop), [
            'status' => 'deleted',
            'is_feature_shop' => 'invalid',
        ])->assertSessionHasErrors(['status', 'is_feature_shop']);

        $this->get('/admin/shops/invalid-key/show')->assertNotFound();
    }
}
