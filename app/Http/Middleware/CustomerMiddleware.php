<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user?->role !== 'customer') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please sign in as a customer to manage your wishlist.',
                    'redirect' => route('login'),
                ], $user ? 403 : 401);
            }

            return redirect()->route('login');
        }

        if (! $user->google_id && ! $user->facebook_id && ! $user->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Verify your email address to manage your wishlist.',
                    'redirect' => route('verification.notice'),
                ], 403);
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
