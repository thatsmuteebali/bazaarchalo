<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

trait HandlesGoogleLogin
{
    protected function googleDriver()
    {
        return Socialite::buildProvider(
            GoogleProvider::class,
            config("services.{$this->googleConfigKey}")
        );
    }

    public function redirectToGoogle()
    {
        return $this->googleDriver()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = $this->googleDriver()->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if ($user->role !== $this->role) {
                return redirect($this->loginRouteName())
                    ->withErrors(['login' => 'This email is already registered as a ' . $user->role . '. Please use a different Google account.']);
            }

            if ($user->status !== 'Active') {
                return redirect($this->loginRouteName())
                    ->withErrors(['login' => 'Your account is inactive.']);
            }

            $user->update(['google_id' => $googleUser->getId()]);
        } else {
            $user = User::create([
                'name'      => $googleUser->getName(),
                'username'  => Str::slug($googleUser->getName()) . '-' . Str::random(4),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'role'      => $this->role,
                'status'    => 'Active',
                'password'  => null,
            ]);
        }

        Auth::login($user);

        return redirect($this->redirectTo);
    }
}
