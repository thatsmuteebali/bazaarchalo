<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_category_can_be_created_updated_and_deleted(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), [
            'name' => '  Pantry  ',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('pantry.jpg'),
        ])
            ->assertRedirect(route('admin.categories.list'));

        $category = Category::where('name', 'Pantry')->firstOrFail();

        $this->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('value="Pantry"', false);

        $this->put(route('admin.categories.update', $category), ['name' => 'Dry Goods', 'status' => 'active'])
            ->assertRedirect(route('admin.categories.list'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Dry Goods']);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.list'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_names_are_required_and_unique(): void
    {
        Category::create(['name' => 'Produce']);

        $this->post(route('admin.categories.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->post(route('admin.categories.store'), ['name' => 'Produce'])
            ->assertSessionHasErrors('name');

        $this->post(route('admin.categories.store'), ['name' => ['invalid']])
            ->assertSessionHasErrors('name');
    }

    public function test_category_slugs_are_generated_unique_and_readonly(): void
    {
        Storage::fake('public');

        $this->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSee('name="slug"', false)
            ->assertSee('readonly', false);

        $this->post(route('admin.categories.store'), [
            'name' => 'Home & Garden',
            'slug' => 'user-provided-slug',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('home-garden.jpg'),
        ])->assertRedirect(route('admin.categories.list'));

        $category = Category::where('name', 'Home & Garden')->firstOrFail();
        $this->assertSame('home-garden', $category->slug);

        $this->post(route('admin.categories.store'), [
            'name' => 'Home Garden',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('home-garden-2.jpg'),
        ])
            ->assertRedirect(route('admin.categories.list'));
        $this->assertDatabaseHas('categories', ['name' => 'Home Garden', 'slug' => 'home-garden-2']);

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Outdoor Living',
            'slug' => 'another-user-provided-slug',
            'status' => 'active',
        ])->assertRedirect(route('admin.categories.list'));

        $category->refresh();
        $this->assertSame('outdoor-living', $category->slug);

        $this->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('value="outdoor-living"', false)
            ->assertSee('readonly', false);
    }

    public function test_category_image_can_be_uploaded_replaced_and_displayed(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), [
            'name' => 'Illustrated Category',
            'image' => UploadedFile::fake()->image('category.jpg'),
            'status' => 'active',
        ])->assertRedirect(route('admin.categories.list'));

        $category = Category::where('name', 'Illustrated Category')->firstOrFail();
        $originalImage = $category->image;
        Storage::disk('public')->assertExists($originalImage);

        $tableResponse = $this->get(route('admin.categories.list', ['draw' => 1, 'start' => 0, 'length' => 10]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();
        $this->assertStringContainsString($originalImage, $tableResponse->json('data.0.image'));

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Illustrated Category',
            'image' => UploadedFile::fake()->image('replacement.jpg'),
            'status' => 'active',
        ])->assertRedirect(route('admin.categories.list'));

        $category->refresh();
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_category_table_filters_searches_and_sorts_without_page_reload(): void
    {
        Category::create(['name' => 'Banana', 'status' => 'active']);
        Category::create(['name' => 'Apple', 'status' => 'active']);
        Category::create(['name' => 'Apricot', 'status' => 'inactive']);

        $this->get(route('admin.categories.list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => 'active',
            'search' => ['value' => 'a', 'regex' => 'false'],
            'order' => [['column' => 2, 'dir' => 'asc']],
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'image', 'name' => 'image', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'name', 'name' => 'categories.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'status', 'name' => 'categories.status', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'sort_id', 'name' => 'categories.id', 'searchable' => 'false', 'orderable' => 'true'],
            ],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('recordsTotal', 3)
            ->assertJsonPath('recordsFiltered', 2)
            ->assertJsonPath('data.0.name', 'Apple')
            ->assertJsonPath('data.1.name', 'Banana')
            ->assertJsonPath('data.0.status', '<span class="badge-table success">Active</span>');
    }
}
