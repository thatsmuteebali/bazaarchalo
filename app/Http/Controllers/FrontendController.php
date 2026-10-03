<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.home');
    }
    public function shop()
    {
        return view('frontend.shop');
    }
    public function about()
    {
        return view('frontend.about');
    }
    public function contact()
    {
        return view('frontend.contact');
    }
    public function productDetail()
    {
        return view('frontend.product-detail');
    }
    public function categories()
    {
        return view('frontend.categories');
    }
    public function deliveryPolicy()
    {
        return view('frontend.delivery-policy');
    }
    public function faq()
    {
        return view('frontend.faq');
    }
    public function offers()
    {
        return view('frontend.offers');
    }
    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }
    public function refundPolicy()
    {
        return view('frontend.refund-policy');
    }
    public function shopListing()
    {
        return view('frontend.shop-listing');
    }
    public function termsAndConditions()
    {
        return view('frontend.terms-and-conditions');
    }
    public function cart()
    {
        return view('frontend.cart');
    }
}
