<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(Request $request)
    {
        $role = $this->roleFrom($request->route('role'));
        $view = $role === 'customer' ? 'auth.passwords.email' : $role.'.passwords.email';

        return view($view, compact('role'));
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        $role = $this->roleFrom($request->route('role'));

        $userExistsForRole = User::query()
            ->where('email', $validated['email'])
            ->where('role', $role)
            ->exists();

        if (! $userExistsForRole) {
            return back()->withInput()->withErrors([
                'email' => 'Email does not exist for this account type.',
            ]);
        }

        $status = Password::sendResetLink(['email' => $validated['email']]);

        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => trans($status)]);
        }

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withInput()->withErrors([
                'email' => 'Email does not exist for this account type.',
            ]);
        }

        return back()->with('status', 'A password reset link has been sent to your email address.');
    }

    private function roleFrom(mixed $role): string
    {
        return is_string($role) && in_array($role, ['customer', 'seller', 'admin'], true)
            ? $role
            : 'customer';
    }
}
