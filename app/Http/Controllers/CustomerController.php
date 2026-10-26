<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderPlacementService;
use App\Exceptions\CartException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function account(){
        return view('customer.account');
    }
    public function profile(){
        return view('customer.profile');
    }
    public function orders(){
        return view('customer.orders');
    }
    public function orderDetail(){
        return view('customer.order-detail');
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
