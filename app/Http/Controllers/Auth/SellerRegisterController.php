<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\Recaptcha;

class SellerRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('seller.register');
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'unique:users', 'regex:/^03\d{9}$/'],
            'shop_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha('seller_register')];
        }

        return Validator::make($data, $rules, [
            'phone.regex' => 'Enter a Pakistani mobile number in the format 03000000000.',
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please refresh and try again.',
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'seller',
            'username' => $request->username,   // in SellerRegisterController use $request->username
            'password' => Hash::make($request->password),
        ]);

        Shop::create([
            'seller_id' => $user->id,
            'name' => $request->shop_name,
            'is_primary' => true,
        ]);

        event(new Registered($user));

        return redirect()->route('seller.login')->with('success', 'Registration successful! Please log in.');
    }
}
