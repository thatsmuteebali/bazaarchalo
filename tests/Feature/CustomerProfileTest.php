<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_profile_form_displays_editable_fields(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'username' => 'profile-customer',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.profile'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="username"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('profile-customer');
    }

    public function test_customer_can_update_profile_without_changing_password(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'username' => 'old-name',
            'password' => 'current-password',
        ]);

        $response = $this->actingAs($customer)->put(route('customer.profile.update'), [
            'name' => 'Updated Customer',
            'username' => 'updated-customer',
            'email' => 'updated@example.com',
            'phone' => '03001234567',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'username' => 'updated-customer',
            'email' => 'updated@example.com',
            'phone' => '03001234567',
        ]);
        $this->assertTrue(Hash::check('current-password', $customer->fresh()->password));
    }

    public function test_customer_can_update_password_from_profile(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->put(route('customer.profile.update'), [
            'name' => $customer->name,
            'username' => 'new-username',
            'email' => $customer->email,
            'phone' => $customer->phone,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertTrue(Hash::check('new-password', $customer->fresh()->password));
    }
}
