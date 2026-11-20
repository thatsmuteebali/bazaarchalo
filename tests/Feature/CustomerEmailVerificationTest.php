<?php

namespace Tests\Feature;

use App\Mail\WelcomeCustomerMail;
use App\Models\User;
use App\Notifications\VerifyAccountEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CustomerEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_customer_registration_sends_verification_email(): void
    {
        Notification::fake();
        config(['services.recaptcha.enabled' => false]);

        $this->post(route('register'), [
            'name' => 'Customer',
            'username' => 'customer1',
            'email' => 'customer@example.com',
            'phone' => '03001234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'term' => 'on',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user = User::where('email', 'customer@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyAccountEmail::class);
        $this->assertNull($user->email_verified_at);
        $this->assertGuest();

        $verificationMail = (new VerifyAccountEmail)->toMail($user);
        $this->assertStringContainsString('Verify your email address', $verificationMail->render());
    }

    public function test_manual_customer_registration_requires_an_email_address(): void
    {
        config(['services.recaptcha.enabled' => false]);

        $this->post(route('register'), [
            'name' => 'Customer',
            'username' => 'customer1',
            'phone' => '03001234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'term' => 'on',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_manual_customer_must_verify_before_using_account_pages(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'customer']);
        Route::middleware('CustomerMiddleware')->get(
            '/_test/customer-account',
            fn () => response()->noContent()
        );

        $this->actingAs($user)
            ->get('/_test/customer-account')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_signed_verification_link_marks_customer_email_as_verified(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->unverified()->create(['role' => 'customer']);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Your email address has been verified.');

        $this->assertNotNull($user->fresh()->email_verified_at);
        Mail::assertQueued(WelcomeCustomerMail::class, function (WelcomeCustomerMail $mail) use ($user) {
            $this->assertStringContainsString("Your account is ready, {$user->name}!", $mail->render());

            return $mail->user->is($user);
        });

        $notification = $admin->notifications()->firstOrFail();
        $this->assertSame('New customer verified', $notification->data['title']);
        $this->assertSame(
            $user->name.' ('.$user->email.') verified their email address.',
            $notification->data['message']
        );

        $this->get($verificationUrl);

        Mail::assertQueuedCount(1);
        $this->assertSame(1, $admin->notifications()->count());
    }

    public function test_seller_registration_event_sends_verification_email(): void
    {
        Notification::fake();
        $seller = User::factory()->unverified()->create(['role' => 'seller']);

        event(new Registered($seller));

        Notification::assertSentTo($seller, VerifyAccountEmail::class);
    }

    public function test_social_customers_are_not_blocked_by_email_verification_middleware(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => 'customer',
            'google_id' => 'google-customer-id',
        ]);
        Route::middleware('CustomerMiddleware')->get(
            '/_test/customer-account',
            fn () => response()->noContent()
        );

        $this->actingAs($user)
            ->get('/_test/customer-account')
            ->assertNoContent();
    }

    public function test_customer_can_request_another_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['role' => 'customer']);

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'A new verification link has been sent to your email address.');

        Notification::assertSentTo($user, VerifyAccountEmail::class);
    }
}
