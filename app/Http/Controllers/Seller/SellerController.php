<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    public function profile(){
        return view('seller.profile');
    }
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($request->user()->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['nullable', 'regex:/^03\d{9}$/', Rule::unique('users')->ignore($request->user()->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], [
            'phone.regex' => 'Enter a Pakistani mobile number in the format 03000000000.',
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user = $request->user();
        $oldImage = $user->image;
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profiles', 'public');
        }

        $user->update($validated);

        if ($request->hasFile('image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('seller.profile')->with('success', 'Your profile has been updated.');
    }
}
