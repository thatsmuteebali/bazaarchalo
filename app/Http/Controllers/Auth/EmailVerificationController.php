<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeCustomerMail;
use App\Mail\WelcomeSellerMail;
use App\Models\User;
use App\Services\MarketplaceNotificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $this->needsEmailVerification($user)) {
            return $this->dashboardRedirect($user);
        }

        return view('auth.verification.notice');
    }

    public function verify(
        EmailVerificationRequest $request,
        MarketplaceNotificationService $notificationService,
    ): RedirectResponse
    {
        $user = $request->user();
        $wasVerified = $user->hasVerifiedEmail();

        $request->fulfill();

        if (! $wasVerified && ! $user->google_id && ! $user->facebook_id) {
            if ($user->role === 'customer') {
                Mail::to($user->email)->queue((new WelcomeCustomerMail($user))->afterCommit());
                $notificationService->notifyAdminsOfVerifiedCustomer($user);
            } elseif ($user->role === 'seller') {
                Mail::to($user->email)->queue((new WelcomeSellerMail($user))->afterCommit());
                $notificationService->notifyAdminsOfVerifiedSeller($user);
            }
        }

        return $this->dashboardRedirect($user)
            ->with('status', 'Your email address has been verified.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $this->needsEmailVerification($user)) {
            return $this->dashboardRedirect($user);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent to your email address.');
    }

    private function needsEmailVerification(User $user): bool
    {
        return in_array($user->role, ['customer', 'seller'], true)
            && ! $user->hasVerifiedEmail()
            && ! $user->google_id
            && ! $user->facebook_id;
    }

    private function dashboardRedirect(User $user): RedirectResponse
    {
        return redirect()->route($user->role === 'seller' ? 'seller.dashboard' : 'home');
    }
}
