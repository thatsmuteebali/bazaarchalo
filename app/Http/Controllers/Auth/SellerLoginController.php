<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\LoginWithUsernameOrEmail;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Auth\Concerns\HandlesSocialLogin;

class SellerLoginController extends Controller
{
    use AuthenticatesUsers, LoginWithUsernameOrEmail, HandlesSocialLogin {
        LoginWithUsernameOrEmail::username insteadof AuthenticatesUsers;
        LoginWithUsernameOrEmail::credentials insteadof AuthenticatesUsers;
    }

    protected $role = 'seller';
    protected $redirectTo = '/seller/dashboard';

    protected function loginRouteName()
    {
        return route('seller.login');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm()
    {
        return view('seller.login');
    }
}
