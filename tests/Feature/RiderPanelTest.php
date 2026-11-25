<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RiderPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        WebContent::create([
            'page' => 'setting',
            'field' => 'Website Header Logo',
            'value' => 'no_image',
            'type' => 'image',
        ]);
    }

    public function test_rider_login_page_uses_rider_route_and_has_no_social_login(): void
    {
        config(['services.recaptcha.enabled' => false]);

        $this->get('/rider/login')
            ->assertOk()
            ->assertSee(route('rider.login'), false)
            ->assertSee('Email, Username or Phone Number')
            ->assertDontSee('Google')
            ->assertDontSee('Facebook')
            ->assertDontSee('Register Now');
    }

    public function test_active_riders_can_login_with_email_username_or_phone_and_logout(): void
    {
        config(['services.recaptcha.enabled' => false]);
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Active',
            'username' => 'delivery-rider',
            'email' => 'delivery-rider@example.com',
            'phone' => '03012345678',
            'password' => 'password123',
        ]);

        foreach ([$rider->email, $rider->username, $rider->phone] as $identifier) {
            $this->post(route('rider.login'), [
                'login' => $identifier,
                'password' => 'password123',
            ])->assertRedirect(route('rider.dashboard'));

            $this->assertAuthenticatedAs($rider);
            $this->post(route('rider.logout'))->assertRedirect(route('rider.login'));
            $this->assertGuest();
        }
    }

    public function test_only_active_rider_accounts_can_login(): void
    {
        config(['services.recaptcha.enabled' => false]);
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);
        $inactiveRider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Inactive',
            'email' => 'inactive-rider@example.com',
            'password' => 'password123',
        ]);

        foreach ([$customer, $inactiveRider] as $user) {
            $this->from(route('rider.login'))
                ->post(route('rider.login'), [
                    'login' => $user->email,
                    'password' => 'password123',
                ])
                ->assertRedirect(route('rider.login'))
                ->assertSessionHasErrors('login');

            $this->assertGuest();
        }
    }

    public function test_rider_recaptcha_is_required_and_validated_for_rider_login_action(): void
    {
        config([
            'services.recaptcha.enabled' => true,
            'services.recaptcha.site_key' => 'test-site-key',
            'services.recaptcha.secret_key' => 'test-secret-key',
        ]);

        $this->get(route('rider.login'))
            ->assertOk()
            ->assertSee('g-recaptcha-response')
            ->assertSee('rider_login');

        $rider = User::factory()->create([
            'role' => 'rider',
            'email' => 'captcha-rider@example.com',
            'password' => 'password123',
        ]);

        $this->from(route('rider.login'))
            ->post(route('rider.login'), [
                'login' => $rider->email,
                'password' => 'password123',
            ])
            ->assertRedirect(route('rider.login'))
            ->assertSessionHasErrors('g-recaptcha-response');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'rider_login',
                'score' => 0.9,
            ]),
        ]);

        $this->post(route('rider.login'), [
            'login' => $rider->email,
            'password' => 'password123',
            'g-recaptcha-response' => 'valid-token',
        ])->assertRedirect(route('rider.dashboard'));

        Http::assertSent(fn ($request) => $request['response'] === 'valid-token');
    }

    public function test_rider_panel_has_only_dashboard_and_orders_sidebar_links(): void
    {
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Active',
        ]);

        $this->actingAs($rider)
            ->get(route('rider.dashboard'))
            ->assertOk()
            ->assertSee(route('rider.dashboard'), false)
            ->assertSee(route('rider.orders.index'), false)
            ->assertDontSee(route('seller.dashboard'), false)
            ->assertDontSee('Products');

        $this->get(route('rider.orders.index'))
            ->assertOk()
            ->assertSee('No assigned orders yet');
    }

    public function test_non_riders_cannot_access_rider_panel(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);

        $this->actingAs($seller)
            ->get(route('rider.dashboard'))
            ->assertRedirect(route('rider.login'));
    }

    public function test_deactivated_rider_sessions_are_logged_out_of_the_panel(): void
    {
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Inactive',
        ]);

        $this->actingAs($rider)
            ->get(route('rider.dashboard'))
            ->assertRedirect(route('rider.login'));

        $this->assertGuest();
    }
}
