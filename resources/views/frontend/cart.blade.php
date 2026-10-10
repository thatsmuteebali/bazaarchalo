@extends('layouts.app')

@section('customCss')
    <style>
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .cart-item-name:hover {
            color: var(--bs-primary) !important;
        }

        .cart-qty {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .cart-qty .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cart-qty-value {
            min-width: 24px;
            text-align: center;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Cart</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.shop') }}">Shop</a></li>
            <li class="breadcrumb-item active text-white">Cart</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            {{-- Rendered by the server. When the cart changes, the JavaScript swaps this block with fresh HTML from the server. --}}
            <div id="cartPageContent">
                @include('frontend.partials.cart-content', ['cart' => $cart])
            </div>
        </div>
    </div>
    <!-- Cart Page End -->
@endsection
