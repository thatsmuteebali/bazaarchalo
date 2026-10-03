<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\FacebookProvider;

trait HandlesSocialLogin
{
    protected function providerClass(string $provider)
    {
        return match ($provider) {
            'google' => GoogleProvider::class,
            'facebook' => FacebookProvider::class,
            default => throw new \InvalidArgumentException("Unsupported provider: {$provider}"),
        };
    }

    protected function socialConfigKey(string $provider): string
    {
        // e.g. 'google' + '' = 'google', or 'google' + '_seller' = 'google_seller'
        return $provider . ($this->role === 'customer' ? '' : "_{$this->role}");
    }

    protected function socialDriver(string $provider)
    {
        return Socialite::buildProvider(
            $this->providerClass($provider),
            config("services.{$this->socialConfigKey($provider)}")
        );
    }

    public function redirectToProvider(string $provider)
    {
        return $this->socialDriver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        // User clicked "Cancel" or denied permissions
        if (request()->has('error')) {
            return redirect($this->loginRouteName())
                ->withErrors(['login' => 'Login was cancelled.']);
        }

        try {
            $socialUser = $this->socialDriver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect($this->loginRouteName())
                ->withErrors(['login' => 'Something went wrong while logging in with ' . ucfirst($provider) . '. Please try again.']);
        }

        $idColumn = "{$provider}_id"; // google_id or facebook_id

        $user = User::where($idColumn, $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            if ($user->role !== $this->role) {
                return redirect($this->loginRouteName())
                    ->withErrors(['login' => 'This email is already registered as a ' . $user->role . '. Please use a different account.']);
            }

            if ($user->status !== 'Active') {
                return redirect($this->loginRouteName())
                    ->withErrors(['login' => 'Your account is inactive.']);
            }

            $user->update([$idColumn => $socialUser->getId()]);
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'username' => Str::slug($socialUser->getName() ?? 'user') . '-' . Str::random(4),
                'email' => $socialUser->getEmail(),
                $idColumn => $socialUser->getId(),
                'role' => $this->role,
                'status' => 'Active',
                'password' => null,
            ]);
        }

        Auth::login($user);

        return redirect($this->redirectTo);
    }
}
