<?php

namespace App\Http\Controllers;

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
    public function checkout(){
        return view('customer.checkout');
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
