<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminRiderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
        WebContent::create([
            'page' => 'setting',
            'field' => 'Website Header Logo',
            'value' => 'no_image',
            'type' => 'image',
        ]);
    }

    public function test_admin_can_create_rider_accounts_and_datatable_only_lists_riders(): void
    {
        Storage::fake('public');

        $this->get(route('admin.riders.create'))
            ->assertOk()
            ->assertSee(route('admin.riders.index'), false)
            ->assertSee('Confirm Password');

        $this->post(route('admin.riders.store'), [
            'name' => 'Rider One',
            'username' => 'rider-one',
            'email' => 'rider@example.com',
            'phone' => '03012345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'Active',
            'image' => UploadedFile::fake()->image('rider.png'),
        ])->assertRedirect(route('admin.riders.index'));

        $rider = User::where('email', 'rider@example.com')->firstOrFail();
        $this->assertSame('rider', $rider->role);
        $this->assertTrue(Hash::check('password123', $rider->password));
        Storage::disk('public')->assertExists($rider->image);

        User::factory()->create(['role' => 'customer', 'name' => 'Not a Rider']);
        $this->get(route('admin.riders.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', 'Rider One')
            ->assertJsonPath('data.0.phone', '03012345678');
    }

    public function test_admin_can_update_and_delete_only_rider_accounts(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('existing-rider.png')->store('riders', 'public');
        $rider = User::factory()->create([
            'role' => 'rider',
            'name' => 'Original Rider',
            'username' => 'original-rider',
            'email' => 'original-rider@example.com',
            'phone' => '03011111111',
            'password' => Hash::make('old-password'),
            'status' => 'Active',
            'image' => $image,
        ]);
        $key = $this->riderKeyFromList($rider->name);

        $this->get(route('admin.riders.show', ['riderKey' => $key]))
            ->assertOk()
            ->assertSee($rider->email);
        $this->get(route('admin.riders.edit', ['riderKey' => $key]))
            ->assertOk()
            ->assertSee('leave blank to keep current');

        $this->put(route('admin.riders.update', ['riderKey' => $key]), [
            'name' => 'Updated Rider',
            'username' => $rider->username,
            'email' => $rider->email,
            'phone' => $rider->phone,
            'status' => 'Inactive',
            'remove_image' => '1',
        ])->assertRedirect(route('admin.riders.index'));

        $this->assertDatabaseHas('users', [
            'id' => $rider->id,
            'role' => 'rider',
            'name' => 'Updated Rider',
            'status' => 'Inactive',
        ]);
        $this->assertTrue(Hash::check('old-password', $rider->fresh()->password));
        $this->assertNull($rider->fresh()->image);
        Storage::disk('public')->assertMissing($image);

        $this->delete(route('admin.riders.destroy', ['riderKey' => $key]))
            ->assertRedirect(route('admin.riders.index'));
        $this->assertDatabaseMissing('users', ['id' => $rider->id]);
    }

    public function test_rider_validation_rejects_duplicate_user_fields_and_invalid_phone(): void
    {
        User::factory()->create([
            'role' => 'customer',
            'username' => 'existing-user',
            'email' => 'existing@example.com',
            'phone' => '03098765432',
        ]);

        $this->from(route('admin.riders.create'))
            ->post(route('admin.riders.store'), [
                'name' => 'Invalid Rider',
                'username' => 'existing-user',
                'email' => 'existing@example.com',
                'phone' => '12345',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'status' => 'Active',
            ])
            ->assertRedirect(route('admin.riders.create'))
            ->assertSessionHasErrors(['username', 'email', 'phone']);

        $this->assertDatabaseMissing('users', ['name' => 'Invalid Rider']);
    }

    private function riderKeyFromList(string $name): string
    {
        $response = $this->get(route('admin.riders.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => $name, 'regex' => 'false'],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        preg_match('#/admin/riders/([^/"]+)/show#', $response->json('data.0.actions'), $matches);

        $this->assertNotEmpty($matches[1] ?? null);
        $rider = User::where('name', $name)->firstOrFail();
        $this->assertStringNotContainsString('/'.$rider->id.'/show', $response->getContent());

        return $matches[1];
    }
}
