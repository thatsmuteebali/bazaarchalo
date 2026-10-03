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
        $this->post(route('admin.categories.store'), ['name' => '  Pantry  '])
            ->assertRedirect(route('admin.categories.list'));

        $category = Category::where('name', 'Pantry')->firstOrFail();

        $this->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('value="Pantry"', false);

        $this->put(route('admin.categories.update', $category), ['name' => 'Dry Goods'])
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

    public function test_category_image_can_be_uploaded_replaced_and_displayed(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), [
            'name' => 'Illustrated Category',
            'image' => UploadedFile::fake()->image('category.jpg'),
        ])->assertRedirect(route('admin.categories.list'));

        $category = Category::where('name', 'Illustrated Category')->firstOrFail();
        $originalImage = $category->image;
        Storage::disk('public')->assertExists($originalImage);

        $this->get(route('admin.categories.list'))
            ->assertOk()
            ->assertSee($originalImage);

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Illustrated Category',
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('admin.categories.list'));

        $category->refresh();
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_category_search_and_pagination_work(): void
    {
        Category::create(['name' => 'Fresh Produce']);

        foreach (range(1, 11) as $number) {
            Category::create(['name' => "Category {$number}"]);
        }

        $this->get(route('admin.categories.list', ['search' => 'Produce']))
            ->assertOk()
            ->assertSee('Fresh Produce')
            ->assertDontSee('Category 1</td>');

        $this->get(route('admin.categories.list', ['page' => 2]))
            ->assertOk()
            ->assertSee('Category 1')
            ->assertSee('page=2');
    }
}
