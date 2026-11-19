<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/home';

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $email = is_string($email) ? $email : null;
        $role = User::where('email', $email)->value('role');
        $role = in_array($role, ['customer', 'seller', 'admin'], true) ? $role : 'customer';
        $view = $role === 'customer' ? 'auth.passwords.reset' : $role.'.passwords.reset';

        return view($view, [
            'token' => $request->route('token'),
            'email' => $email,
            'role' => $role,
        ]);
    }
}
