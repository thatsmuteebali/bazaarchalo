<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\LoginWithUsernameOrEmail;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Auth\Concerns\HandlesSocialLogin;

class LoginController extends Controller
{
    use AuthenticatesUsers, LoginWithUsernameOrEmail, HandlesSocialLogin {
        LoginWithUsernameOrEmail::username insteadof AuthenticatesUsers;
        LoginWithUsernameOrEmail::credentials insteadof AuthenticatesUsers;
    }

    protected $role = 'customer';
    protected $redirectTo = '/home';

    protected function loginRouteName()
    {
        return route('login');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
