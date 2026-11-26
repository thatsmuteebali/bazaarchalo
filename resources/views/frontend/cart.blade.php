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

        .cart-clear-modal .modal-content {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.18);
        }

        .cart-clear-modal-icon {
            width: 68px;
            height: 68px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(var(--bs-danger-rgb), 0.1);
            color: var(--bs-danger);
            font-size: 1.5rem;
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

    <div class="modal fade cart-clear-modal" id="clearCartModal" tabindex="-1"
        aria-labelledby="clearCartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 justify-content-end pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pt-0 pb-4">
                    <div class="cart-clear-modal-icon mb-3" aria-hidden="true">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h2 class="h4 mb-2" id="clearCartModalLabel">Clear your cart?</h2>
                    <p class="text-muted mb-4">
                        All items will be removed from your cart.
                    </p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill flex-fill" data-bs-dismiss="modal">
                            Keep items
                        </button>
                        <button type="button" class="btn btn-danger rounded-pill flex-fill" id="confirmCartClear">
                            <span class="clear-cart-confirm-label">Clear cart</span>
                            <span class="clear-cart-confirm-loading d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"
                                    aria-hidden="true"></span>Clearing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
