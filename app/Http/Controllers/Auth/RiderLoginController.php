<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\LoginWithUsernameOrEmail;
use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderLoginController extends Controller
{
    use AuthenticatesUsers, LoginWithUsernameOrEmail {
        LoginWithUsernameOrEmail::username insteadof AuthenticatesUsers;
        LoginWithUsernameOrEmail::credentials insteadof AuthenticatesUsers;
    }

    protected $role = 'rider';

    protected $redirectTo = '/rider/dashboard';

    protected function loginRouteName()
    {
        return route('rider.login');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('rider.login');
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha('rider_login')];
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please refresh and try again.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('rider.login');
    }
}
