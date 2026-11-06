<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CollectionCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'seller']));
    }

    public function test_seller_can_create_view_update_search_and_delete_a_collection(): void
    {
        Storage::fake('public');

        $this->get(route('seller.collections.create'))
            ->assertOk()
            ->assertSee('Create Collection');

        $this->post(route('seller.collections.store'), [
            'name' => 'Autumn Picks',
            'description' => 'Seasonal favorites.',
            'slug' => 'autumn-picks',
            'status' => 'active',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->image('collection.jpg'),
        ])->assertRedirect(route('seller.collections.index'));

        $collection = Collection::where('name', 'Autumn Picks')->firstOrFail();
        $this->assertSame(auth()->id(), $collection->seller_id);
        $this->assertSame('pending', $collection->status);
        $this->assertFalse($collection->is_featured);
        $originalImage = $collection->image;
        Storage::disk('public')->assertExists($originalImage);

        $this->get(route('seller.collections.index'))->assertOk()->assertSee('collection-table');
        $this->get(route('seller.collections.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', 'Autumn Picks')
            ->assertJsonPath('data.0.status', '<span class="badge-table pending">Pending</span>');

        $this->get(route('seller.collections.show', $collection))
            ->assertOk()
            ->assertSee('Seasonal favorites.');

        $this->get(route('seller.collections.edit', $collection))
            ->assertOk()
            ->assertSee('Save Changes');

        $this->put(route('seller.collections.update', [
            'collection' => $collection,
            'search' => 'Autumn',
            'page' => 2,
        ]), [
            'name' => 'Autumn Favorites',
            'description' => 'Updated seasonal favorites.',
            'slug' => 'autumn-favorites',
            'status' => 'active',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('seller.collections.index', ['search' => 'Autumn', 'page' => 2]));

        $collection->refresh();
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($collection->image);
        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => 'Autumn Favorites',
            'slug' => 'autumn-favorites',
            'status' => 'pending',
            'is_featured' => false,
        ]);

        $this->delete(route('seller.collections.destroy', [
            'collection' => $collection,
            'search' => 'Autumn',
            'page' => 2,
        ]))->assertRedirect(route('seller.collections.index', ['search' => 'Autumn', 'page' => 2]));

        Storage::disk('public')->assertExists($collection->image);
        $this->assertSoftDeleted('collections', ['id' => $collection->id]);
        $this->get(route('seller.collections.show', $collection))->assertNotFound();

        $this->patch(route('seller.collections.restore', $collection))
            ->assertRedirect(route('seller.collections.index'));
        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'deleted_at' => null]);

        $this->delete(route('seller.collections.destroy', $collection))
            ->assertRedirect(route('seller.collections.index'));
        $this->delete(route('seller.collections.force-delete', $collection))
            ->assertRedirect(route('seller.collections.index'));
        Storage::disk('public')->assertMissing($collection->image);
        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function test_collection_slug_must_be_unique(): void
    {
        Storage::fake('public');
        auth()->user()->collections()->create([
            'name' => 'Existing Collection',
            'slug' => 'existing-collection',
        ]);

        $this->from(route('seller.collections.create'))
            ->post(route('seller.collections.store'), [
                'name' => 'Duplicate Collection',
                'slug' => 'existing-collection',
                'image' => UploadedFile::fake()->image('collection.jpg'),
            ])
            ->assertRedirect(route('seller.collections.create'))
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing('collections', ['name' => 'Duplicate Collection']);
    }

    public function test_seller_cannot_access_another_sellers_collection(): void
    {
        $anotherSeller = User::factory()->create(['role' => 'seller']);
        $collection = $anotherSeller->collections()->create(['name' => 'Private Collection']);

        $this->get(route('seller.collections.show', $collection))->assertNotFound();
        $this->get(route('seller.collections.edit', $collection))->assertNotFound();
        $this->put(route('seller.collections.update', $collection), [
            'name' => 'Changed Collection',
        ])->assertNotFound();
        $this->delete(route('seller.collections.destroy', $collection))->assertNotFound();
        $this->patch(route('seller.collections.restore', $collection))->assertNotFound();
        $this->delete(route('seller.collections.force-delete', $collection))->assertNotFound();

        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'name' => 'Private Collection']);
    }

    public function test_collection_routes_and_datatable_do_not_expose_database_ids(): void
    {
        $collection = auth()->user()->collections()->create([
            'name' => 'Encrypted Route Collection',
            'slug' => 'encrypted-route-collection',
            'status' => 'active',
        ]);
        $showUrl = route('seller.collections.show', $collection);

        $this->assertStringNotContainsString('/'.$collection->id, $showUrl);
        $this->get($showUrl)->assertOk()->assertSee('Encrypted Route Collection');
        $this->get('/seller/collections/not-a-valid-encrypted-id')->assertNotFound();

        $response = $this->get(route('seller.collections.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $this->assertArrayNotHasKey('id', $response->json('data.0'));
        preg_match('#/seller/collections/([^/"]+)#', $response->json('data.0.actions'), $routeMatch);
        $this->assertNotEmpty($routeMatch[1] ?? null);
        $this->assertNotSame((string) $collection->id, $routeMatch[1]);
    }

    public function test_collection_table_filters_trashed_collections(): void
    {
        $seller = auth()->user();
        $activeCollection = $seller->collections()->create([
            'name' => 'Active Collection',
            'slug' => 'active-collection',
            'status' => 'active',
        ]);
        $trashedCollection = $seller->collections()->create([
            'name' => 'Trashed Collection',
            'slug' => 'trashed-collection',
            'status' => 'active',
        ]);
        $trashedCollection->delete();

        $request = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ];

        $activeResponse = $this->get(route('seller.collections.index', $request), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $activeResponse->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $activeCollection->name);

        $trashedResponse = $this->get(route('seller.collections.index', $request + ['status' => 'trashed']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $trashedResponse->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $trashedCollection->name);
        preg_match('#/seller/collections/([^/]+)/restore#', $trashedResponse->json('data.0.actions'), $routeMatch);
        $this->assertNotEmpty($routeMatch[1] ?? null);
        $this->assertNotSame((string) $trashedCollection->id, $routeMatch[1]);
    }
}
