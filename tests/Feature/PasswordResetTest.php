<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_has_its_own_password_recovery_page(): void
    {
        $pages = [
            ['password.request', 'password.email', 'Forgot your password?'],
            ['seller.password.request', 'seller.password.email', 'Seller Password Recovery'],
            ['admin.password.request', 'admin.password.email', 'Admin Password Recovery'],
        ];

        foreach ($pages as [$requestRoute, $emailRoute, $heading]) {
            $this->get(route($requestRoute))
                ->assertOk()
                ->assertSee($heading)
                ->assertSee(route($emailRoute), false);
        }
    }

    public function test_recovery_pages_reject_emails_belonging_to_other_roles(): void
    {
        Notification::fake();
        $users = [
            'customer' => User::factory()->create(['role' => 'customer']),
            'seller' => User::factory()->create(['role' => 'seller']),
            'admin' => User::factory()->create(['role' => 'admin']),
        ];
        $routes = [
            'customer' => 'password.email',
            'seller' => 'seller.password.email',
            'admin' => 'admin.password.email',
        ];
        $otherRoles = ['customer' => 'seller', 'seller' => 'admin', 'admin' => 'customer'];

        foreach ($otherRoles as $role => $otherRole) {
            $this->post(route($routes[$role]), ['email' => $users[$otherRole]->email])
                ->assertRedirect()
                ->assertSessionHasErrors([
                    'email' => 'Email does not exist for this account type.',
                ]);
        }

        Notification::assertNothingSent();
    }

    public function test_matching_customer_seller_and_admin_accounts_receive_reset_links(): void
    {
        Notification::fake();
        $accounts = [
            ['role' => 'customer', 'route' => 'password.email'],
            ['role' => 'seller', 'route' => 'seller.password.email'],
            ['role' => 'admin', 'route' => 'admin.password.email'],
        ];

        foreach ($accounts as $account) {
            $user = User::factory()->create(['role' => $account['role']]);

            $this->post(route($account['route']), ['email' => $user->email])
                ->assertRedirect()
                ->assertSessionHas('status', 'A password reset link has been sent to your email address.');

            Notification::assertSentTo($user, ResetPassword::class);
        }
    }

    public function test_reset_links_render_the_correct_role_specific_page(): void
    {
        Notification::fake();
        $accounts = [
            ['role' => 'customer', 'route' => 'password.email', 'title' => 'Reset Customer Password'],
            ['role' => 'seller', 'route' => 'seller.password.email', 'title' => 'Reset Seller Password'],
            ['role' => 'admin', 'route' => 'admin.password.email', 'title' => 'Reset Admin Password'],
        ];

        foreach ($accounts as $account) {
            $user = User::factory()->create(['role' => $account['role']]);
            $this->post(route($account['route']), ['email' => $user->email]);
            $notification = Notification::sent($user, ResetPassword::class)->first();

            $this->get(route('password.reset', [
                'token' => $notification->token,
                'email' => $user->email,
            ]))->assertOk()->assertSee($account['title']);
        }
    }

    public function test_reset_link_updates_the_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['role' => 'admin']);

        $this->post(route('admin.password.email'), ['email' => $user->email]);
        $notification = Notification::sent($user, ResetPassword::class)->first();

        $this->get(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]))->assertOk()->assertSee('Reset Admin Password');

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'FreshPassword123!',
            'password_confirmation' => 'FreshPassword123!',
        ])->assertRedirect('/home')
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('FreshPassword123!', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
    }
}
