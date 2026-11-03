<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Http\Request;

trait LoginWithUsernameOrEmail
{
    /**
     * The form input name that holds the email, username or phone.
     */
    public function username()
    {
        return 'login';
    }

    protected function credentials(Request $request)
    {
        $login = trim($request->input('login'));

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif ($phone = $this->normalizePhone($login)) {
            $field = 'phone';
            $login = $phone;
        } else {
            $field = 'username';
        }

        return [
            $field     => $login,
            'password' => $request->input('password'),
            'role'     => $this->role,   // set in each controller
            'status'   => 'Active',      // inactive users can't log in
        ];
    }

    /**
     * Convert a Pakistani mobile number to the stored format (03XXXXXXXXX).
     * Returns null if the value is not a valid mobile number.
     */
    protected function normalizePhone(string $value): ?string
    {
        if (!preg_match('/^\+?[\d\s\-\(\)]+$/', $value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (str_starts_with($digits, '0092')) {
            $digits = '0' . substr($digits, 4);        // 00923001234567 -> 03001234567
        } elseif (str_starts_with($digits, '92') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);        // 923001234567   -> 03001234567
        } elseif (strlen($digits) === 10 && $digits[0] === '3') {
            $digits = '0' . $digits;                   // 3001234567     -> 03001234567
        }

        return preg_match('/^03\d{9}$/', $digits) ? $digits : null;
    }
}
