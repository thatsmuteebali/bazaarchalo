<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebContent;
use App\Notifications\MarketplaceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        WebContent::create([
            'page' => 'setting',
            'field' => 'Website Favicon',
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
            ->assertSee('No orders have been assigned to you yet.');
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

    public function test_riders_can_view_and_update_their_profile(): void
    {
        Storage::fake('public');
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Active',
            'name' => 'Original Rider',
            'username' => 'original-rider',
            'email' => 'original-rider@example.com',
            'phone' => '03012345678',
            'password' => 'old-password',
        ]);
        $newImage = UploadedFile::fake()->image('rider-profile.jpg');

        $this->actingAs($rider)
            ->get(route('rider.profile'))
            ->assertOk()
            ->assertSee('Manage your rider account details.')
            ->assertSee(route('rider.profile.update'), false);

        $this->put(route('rider.profile.update'), [
            'name' => 'Updated Rider',
            'username' => 'updated-rider',
            'email' => 'updated-rider@example.com',
            'phone' => '03098765432',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'image' => $newImage,
        ])->assertRedirect(route('rider.profile'));

        $rider->refresh();
        $this->assertSame('Updated Rider', $rider->name);
        $this->assertSame('updated-rider', $rider->username);
        $this->assertSame('updated-rider@example.com', $rider->email);
        $this->assertSame('03098765432', $rider->phone);
        $this->assertTrue(Hash::check('new-password', $rider->password));
        Storage::disk('public')->assertExists($rider->image);
    }

    public function test_rider_notifications_show_in_dropdown_and_can_be_read(): void
    {
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'Active',
        ]);
        $rider->notify(new MarketplaceNotification(
            'Delivery assigned',
            'A new delivery is ready for you.',
            route('rider.orders.index', [], false),
            'bi bi-receipt',
        ));
        $notificationId = $rider->notifications()->firstOrFail()->id;

        $this->actingAs($rider)
            ->get(route('rider.dashboard'))
            ->assertOk()
            ->assertSee('Delivery assigned')
            ->assertSee('navbar-action-badge">1</span>', false);

        $this->get(route('rider.notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('A new delivery is ready for you.');

        $this->post(route('rider.notifications.read', $notificationId))
            ->assertRedirect(route('rider.orders.index'));
        $this->assertNotNull($rider->notifications()->firstOrFail()->read_at);

        $rider->notify(new MarketplaceNotification(
            'Another update',
            'A second rider update.',
            route('rider.dashboard', [], false),
            'bi bi-bell',
        ));
        $this->post(route('rider.notifications.read-all'))
            ->assertRedirect(route('rider.notifications.index'));
        $this->assertSame(0, $rider->unreadNotifications()->count());
    }
}
