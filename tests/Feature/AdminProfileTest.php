<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_profile_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => '',
            'password_confirmation' => '',
            'image' => UploadedFile::fake()->image('profile.jpg'),
        ]);

        $response->assertRedirect(route('admin.profile'));
        $image = $admin->fresh()->image;
        $this->assertNotNull($image);
        Storage::disk('public')->assertExists($image);
    }
}
