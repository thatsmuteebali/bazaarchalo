<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['role' => 'admin']
        );
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha('admin_login')];
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please refresh and try again.',
        ]);
    }
}
