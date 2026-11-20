<?php

namespace Tests\Feature;

use App\Mail\WelcomeSellerMail;
use App\Models\User;
use App\Notifications\VerifyAccountEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SellerEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_seller_registration_sends_a_verification_email(): void
    {
        Notification::fake();
        config(['services.recaptcha.enabled' => false]);

        $this->post(route('seller.register'), [
            'name' => 'Green Market',
            'username' => 'greenmarket',
            'email' => 'seller@example.com',
            'phone' => '03001234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'Green Market',
        ])->assertRedirect(route('seller.login'))
            ->assertSessionHas('success', 'Registration successful! Please check your email for a verification link, then log in.');

        $seller = User::where('email', 'seller@example.com')->firstOrFail();

        $this->assertSame('seller', $seller->role);
        $this->assertNull($seller->email_verified_at);
        $this->assertDatabaseHas('shops', [
            'seller_id' => $seller->id,
            'name' => 'Green Market',
        ]);
        Notification::assertSentTo($seller, VerifyAccountEmail::class);
    }

    public function test_manual_seller_must_verify_before_using_seller_pages(): void
    {
        $seller = User::factory()->unverified()->create(['role' => 'seller']);
        Route::middleware('SellerMiddleware')->get(
            '/_test/seller-dashboard',
            fn () => response()->noContent()
        );

        $this->actingAs($seller)
            ->get('/_test/seller-dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_seller_verification_sends_welcome_email_and_admin_notification_once(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->unverified()->create([
            'role' => 'seller',
            'status' => 'Active',
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $seller->id, 'hash' => sha1($seller->email)]
        );

        $this->get($verificationUrl)
            ->assertRedirect(route('seller.login'));

        $this->post(route('seller.login'), [
            'login' => $seller->email,
            'password' => 'password',
        ])->assertRedirect($verificationUrl);

        $this->get($verificationUrl)
            ->assertRedirect(route('seller.dashboard'))
            ->assertSessionHas('status', 'Your email address has been verified.');

        $this->assertNotNull($seller->fresh()->email_verified_at);
        Mail::assertQueued(WelcomeSellerMail::class, function (WelcomeSellerMail $mail) use ($seller) {
            $this->assertStringContainsString("seller account is ready, {$seller->name}!", $mail->render());

            return $mail->user->is($seller);
        });

        $notification = $admin->notifications()->firstOrFail();
        $this->assertSame('New seller verified', $notification->data['title']);
        $this->assertSame(
            $seller->name.' ('.$seller->email.') verified their email address.',
            $notification->data['message']
        );

        $this->get($verificationUrl);

        Mail::assertQueuedCount(1);
        $this->assertSame(1, $admin->notifications()->count());
    }

    public function test_social_sellers_are_not_blocked_by_email_verification(): void
    {
        $seller = User::factory()->unverified()->create([
            'role' => 'seller',
            'facebook_id' => 'facebook-seller-id',
        ]);
        Route::middleware('SellerMiddleware')->get(
            '/_test/seller-dashboard',
            fn () => response()->noContent()
        );

        $this->actingAs($seller)
            ->get('/_test/seller-dashboard')
            ->assertNoContent();
    }
}
