<?php

namespace Tests\Feature;

use App\Mail\AccountStatusMail;
use App\Models\Category;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use App\Services\MarketplaceNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_categories_notify_only_active_sellers(): void
    {
        $category = Category::create([
            'name' => 'Fresh Produce',
            'slug' => 'fresh-produce',
            'status' => 'active',
        ]);
        $activeSeller = User::factory()->create(['role' => 'seller', 'status' => 'Active']);
        $inactiveSeller = User::factory()->create(['role' => 'seller', 'status' => 'Inactive']);

        app(MarketplaceNotificationService::class)->notifyActiveSellersOfCategory($category);

        $this->assertCount(1, $activeSeller->notifications);
        $this->assertSame('New category available', $activeSeller->notifications->first()->data['title']);
        $this->assertCount(0, $inactiveSeller->notifications);
    }

    public function test_pending_submissions_notify_all_admins(): void
    {
        $activeAdmin = User::factory()->create(['role' => 'admin', 'status' => 'Active']);
        $inactiveAdmin = User::factory()->create(['role' => 'admin', 'status' => 'Inactive']);
        $seller = User::factory()->create(['role' => 'seller']);

        app(MarketplaceNotificationService::class)->notifyAdminsOfPendingSubmission(
            'shop',
            'Green Market',
            $seller,
            '/admin/shops',
            'bi bi-shop',
        );

        $this->assertCount(1, $activeAdmin->notifications);
        $this->assertSame('New shop pending review', $activeAdmin->notifications->first()->data['title']);
        $this->assertCount(1, $inactiveAdmin->notifications);
    }

    public function test_status_changes_notify_the_user_and_queue_an_email(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'Active']);
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'Active']);
        $userKey = rtrim(strtr(Crypt::encryptString((string) $seller->id), '+/', '-_'), '=');

        $this->actingAs($admin)
            ->put(route('admin.sellers.update', ['userKey' => $userKey]), ['status' => 'Inactive'])
            ->assertRedirect(route('admin.sellers.index'));

        $seller->refresh();
        $this->assertSame('Inactive', $seller->status);
        $this->assertSame('Your account is inactive', $seller->notifications()->first()->data['title']);
        Mail::assertQueued(AccountStatusMail::class, fn (AccountStatusMail $mail) => $mail->user->is($seller)
            && $mail->status === 'Inactive');
    }

    public function test_notification_read_actions_are_scoped_to_the_signed_in_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $notification = new MarketplaceNotification(
            'Review pending',
            'A shop needs review.',
            route('admin.dashboard', [], false),
            'bi bi-shop',
        );
        $admin->notify($notification);
        $otherAdmin->notify($notification);
        $notificationId = $admin->notifications()->firstOrFail()->id;

        $this->actingAs($admin)
            ->post(route('admin.notifications.read', $notificationId))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertNotNull($admin->notifications()->firstOrFail()->read_at);
        $this->assertNull($otherAdmin->notifications()->firstOrFail()->read_at);

        $this->post(route('admin.notifications.read-all'))->assertRedirect(route('admin.notifications.index'));
        $this->assertSame(0, $admin->unreadNotifications()->count());
        $this->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('Notifications');
    }

    public function test_seller_notification_dropdown_displays_live_notifications_and_unread_count(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->notify(new MarketplaceNotification(
            'Your shop is active',
            'Your shop "Green Market" is now active.',
            route('seller.dashboard', [], false),
            'bi bi-shop',
        ));

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Your shop is active')
            ->assertSee('Green Market')
            ->assertSee('navbar-action-badge">1</span>', false);

        $this->get(route('seller.notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Your shop is active');
    }

    public function test_customers_can_view_their_account_notifications(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $customer->notify(new MarketplaceNotification(
            'Your account is inactive',
            'Your Bazaar Chalo account has been deactivated.',
            route('frontend.home', [], false),
            'bi bi-person-x',
        ));

        $this->actingAs($customer)
            ->get(route('customer.notifications.index'))
            ->assertOk()
            ->assertSee('Your account is inactive')
            ->assertSee('Notifications');
    }
}
