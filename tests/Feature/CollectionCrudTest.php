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
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'image' => UploadedFile::fake()->image('collection.jpg'),
        ])->assertRedirect(route('seller.collections.index'));

        $collection = Collection::where('name', 'Autumn Picks')->firstOrFail();
        $this->assertSame(auth()->id(), $collection->seller_id);
        $originalImage = $collection->image;
        Storage::disk('public')->assertExists($originalImage);

        $this->get(route('seller.collections.index', ['search' => 'Autumn']))
            ->assertOk()
            ->assertSee('Autumn Picks');

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
            'start_date' => '2026-10-02',
            'end_date' => '2026-11-01',
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('seller.collections.index', ['search' => 'Autumn', 'page' => 2]));

        $collection->refresh();
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($collection->image);
        $this->assertSame('2026-10-02', $collection->start_date->format('Y-m-d'));
        $this->assertSame('2026-11-01', $collection->end_date->format('Y-m-d'));
        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => 'Autumn Favorites',
        ]);

        $this->delete(route('seller.collections.destroy', [
            'collection' => $collection,
            'search' => 'Autumn',
            'page' => 2,
        ]))->assertRedirect(route('seller.collections.index', ['search' => 'Autumn', 'page' => 2]));

        Storage::disk('public')->assertMissing($collection->image);
        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function test_collection_end_date_cannot_be_before_start_date(): void
    {
        $this->from(route('seller.collections.create'))
            ->post(route('seller.collections.store'), [
                'name' => 'Invalid Date Range',
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-09',
            ])
            ->assertRedirect(route('seller.collections.create'))
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseMissing('collections', ['name' => 'Invalid Date Range']);
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

        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'name' => 'Private Collection']);
    }
}
