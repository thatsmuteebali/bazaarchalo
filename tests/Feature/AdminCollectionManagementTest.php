<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCollectionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_sidebar_links_to_collection_management(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.collections.index'), false);
    }

    public function test_collection_datatable_shows_seller_status_and_featured_flag(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Collection Owner']);
        $collection = $seller->collections()->create([
            'name' => 'Admin Managed Collection',
            'slug' => 'admin-managed-collection',
            'status' => 'pending',
            'image' => 'collections/admin-managed.jpg',
            'is_featured' => true,
        ]);

        $response = $this->get(route('admin.collections.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $collection->name)
            ->assertJsonPath('data.0.seller_name', $seller->name)
            ->assertJsonPath('data.0.status', '<span class="badge-table pending">Pending</span>')
            ->assertJsonPath('data.0.is_featured', 'Yes');
        $this->assertStringContainsString('storage/collections/admin-managed.jpg', $response->json('data.0.image'));
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
    }

    public function test_admin_can_view_collection_and_update_only_status_and_featured_flag(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'name' => 'Collection Owner']);
        $collection = $seller->collections()->create([
            'name' => 'Managed Collection',
            'slug' => 'managed-collection',
            'description' => 'Seller entered details.',
            'status' => 'pending',
        ]);

        $this->get(route('admin.collections.show', $collection))
            ->assertOk()
            ->assertSee('Collection Owner')
            ->assertSee('Seller entered details.')
            ->assertSee('Featured Collection');

        $this->get(route('admin.collections.edit', $collection))
            ->assertOk()
            ->assertSee('name="status"', false)
            ->assertSee('name="is_featured"', false)
            ->assertSee('value="Managed Collection" readonly', false)
            ->assertSee('value="managed-collection" readonly', false);

        $this->put(route('admin.collections.update', $collection), [
            'status' => 'active',
            'is_featured' => '1',
            'name' => 'Should Not Change',
            'seller_id' => 999,
        ])->assertRedirect(route('admin.collections.index'));

        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => 'Managed Collection',
            'seller_id' => $seller->id,
            'status' => 'active',
            'is_featured' => true,
        ]);
    }

    public function test_admin_rejects_invalid_collection_settings(): void
    {
        $collection = User::factory()->create(['role' => 'seller'])
            ->collections()
            ->create(['name' => 'Collection', 'status' => 'pending']);

        $this->put(route('admin.collections.update', $collection), [
            'status' => 'deleted',
            'is_featured' => 'invalid',
        ])->assertSessionHasErrors(['status', 'is_featured']);
    }
}
