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
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required'],
            'shop_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users'],
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone_number,
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
