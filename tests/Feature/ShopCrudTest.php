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
            'status' => 'active',
            'banner' => UploadedFile::fake()->image('banner.jpg'),
        ])->assertRedirect(route('seller.shops.index'));

        $shop = Shop::where('name', 'Green Market')->firstOrFail();
        $this->assertSame(auth()->id(), $shop->seller_id);
        $originalBanner = $shop->banner;
        Storage::disk('public')->assertExists($originalBanner);

        $this->get(route('seller.shops.show', $shop))
            ->assertOk()
            ->assertSee('Green Market');

        $this->put(route('seller.shops.update', $shop), [
            'name' => 'Green Market Online',
            'description' => 'Updated description.',
            'address' => '14 Market Street',
            'status' => 'inactive',
            'banner' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('seller.shops.index'));

        $shop->refresh();
        Storage::disk('public')->assertMissing($originalBanner);
        Storage::disk('public')->assertExists($shop->banner);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Green Market Online',
            'status' => 'inactive',
        ]);

        $this->delete(route('seller.shops.destroy', [
            'shop' => $shop,
            'search' => 'Green',
            'status' => 'inactive',
            'page' => 3,
        ]))->assertRedirect(route('seller.shops.index', [
            'search' => 'Green',
            'status' => 'inactive',
            'page' => 3,
        ]));

        Storage::disk('public')->assertMissing($shop->banner);
        $this->assertDatabaseMissing('shops', ['id' => $shop->id]);
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

        $this->assertDatabaseHas('shops', ['id' => $otherShop->id, 'name' => 'Private Shop']);
    }
}
