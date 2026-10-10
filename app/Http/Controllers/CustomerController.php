<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutService;
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
    public function orderTracking(){
        return view('customer.order-tracking');
    }

    public function checkout(CartService $cart, CheckoutService $checkout)
    {
        $summary = $cart->summary();

        // nothing to pay for: send the customer back to the cart
        if (empty($summary['items'])) {
            return redirect()->route('frontend.cart');
        }

        return view('customer.checkout', [
            'cart'      => $summary,
            'totals'    => $checkout->totals($summary),
            'provinces' => config('shop.provinces'),
            'cities'    => config('shop.cities'),
            'payments'  => config('shop.payment_methods'),
            'user'      => auth()->user(),
        ]);
    }

    public function orderSuccess(){
        return view('customer.order-success');
    }
    public function wishlist(){
        return view('customer.wishlist');
    }
    public function addresses(){
        return view('customer.addresses');
    }
}
