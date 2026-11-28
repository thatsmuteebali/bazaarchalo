@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Wishlist</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.account') }}">My Account</a></li>
            <li class="breadcrumb-item active text-white">Wishlist</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')
                <section class="col-lg-8 col-xl-9">
                    <div class="mb-4">
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Saved for later
                        </p>
                        <h2 class="display-6 mb-2">Wishlist</h2>
                        <p class="text-muted mb-0">
                            Your favourite local products, ready when you are.
                        </p>
                    </div>
                    <div class="row g-4" data-wishlist-items>
                        @foreach ($wishlistItems as $wishlistItem)
                            <div class="col-md-6 col-lg-6 col-xl-4" data-wishlist-card>
                                @include('frontend.partials.product-card', ['product' => $wishlistItem->product])
                            </div>
                        @endforeach
                        <div class="col-12 {{ $wishlistItems->isEmpty() ? '' : 'd-none' }}" data-wishlist-empty-state>
                            <div class="alert alert-light border mb-0">
                                Your wishlist is empty.
                                <a href="{{ route('frontend.shop') }}" class="alert-link">Browse products</a>
                                to find something you love.
                            </div>
                        </div>
                        <div class="col-12 d-none" data-wishlist-page-empty-state>
                            <div class="alert alert-light border mb-0">
                                There are no more wishlist products on this page.
                            </div>
                        </div>
                        @if ($wishlistItems->hasPages())
                            <div class="col-12 d-flex justify-content-center mt-4" data-wishlist-pagination>
                                {{ $wishlistItems->links() }}
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
