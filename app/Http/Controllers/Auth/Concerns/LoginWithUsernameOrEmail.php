<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Http\Request;

trait LoginWithUsernameOrEmail
{
    /**
     * The form input name that holds the email or username.
     */
    public function username()
    {
        return 'login';
    }

    protected function credentials(Request $request)
    {
        $login = $request->input('login');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field     => $login,
            'password' => $request->input('password'),
            'role'     => $this->role,   // set in each controller
            'status'   => 'Active',      // inactive users can't log in
        ];
    }
}
