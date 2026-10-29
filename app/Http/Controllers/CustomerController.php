<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderPlacementService;
use App\Exceptions\CartException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function account(){
        return view('customer.account');
    }
    public function profile(){
        return view('customer.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($request->user()->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $request->user()->update($validated);

        return redirect()->route('customer.profile')->with('status', 'Your profile has been updated.');
    }

    public function orders(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->withSum('items', 'quantity')
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    public function orderDetail(Request $request)
    {
        $order = $this->customerOrder($request, false);
        if (! $order) {
            return redirect()->route('customer.orders')->with('status', 'That order could not be found in your account.');
        }

        return view('customer.order-detail', compact('order'));
    }
    public function orderTracking(Request $request)
    {
        $order = $this->customerOrder($request, true);
        $tracking = $order
            ? $order->tracking()->reorder()->where('is_public', true)->orderBy('created_at')->orderBy('id')->get()
            : collect();

        return view('customer.order-tracking', [
            'order' => $order,
            'tracking' => $tracking,
            'orderNotFound' => $request->filled('order') && ! $order,
        ]);
    }

    public function checkout(CartService $cart, CheckoutService $checkout)
    {
        $summary = $cart->summary();

        // nothing to pay for: send the customer back to the cart
        if (empty($summary['items'])) {
            return redirect()->route('frontend.cart');
        }

        return view('customer.checkout', [
            'cart'             => $summary,
            'totals'           => $checkout->totals($summary),
            'googleMapsApiKey' => config('services.google_maps.key'),
            'user'              => auth()->user(),
        ]);
    }

    public function placeOrder(CheckoutRequest $request, CartService $cart, OrderPlacementService $orders)
    {
        try {
            $order = $orders->place($request->user(), $cart->summary(), $request->validated());
        } catch (CartException $exception) {
            return back()->withInput()->withErrors(['cart' => $exception->getMessage()]);
        }

        $cart->clear();

        return redirect()->route('customer.order-success', ['order' => $order->order_number]);
    }

    public function orderSuccess(Request $request)
    {
        $order = $this->customerOrder($request, false);
        if (! $order) {
            return redirect()->route('customer.orders')->with('status', 'No order confirmation was found.');
        }

        return view('customer.order-success', compact('order'));
    }
    public function wishlist(){
        return view('customer.wishlist');
    }
    public function addresses(){
        return view('customer.addresses');
    }

    private function customerOrder(Request $request, bool $latestWhenMissing): ?Order
    {
        $query = Order::query()->where('user_id', $request->user()->id);
        if ($request->filled('order')) {
            return $query->with(['items', 'payments'])
                ->where('order_number', $request->query('order'))
                ->first();
        }

        return $latestWhenMissing
            ? $query->with(['items', 'payments'])->latest()->first()
            : null;
    }
}
