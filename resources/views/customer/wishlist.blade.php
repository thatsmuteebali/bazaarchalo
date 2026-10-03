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
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <div class="product-card">
                                <span class="product-badge product-badge-new">New</span>
                                <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                                    <i class="far fa-heart" aria-hidden="true"></i>
                                </button>
                                <div class="product-media">
                                    <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Seedless Grapes">
                                        <img src="{{ asset('img/fruite-item-5.jpg') }}" class="product-image product-image-primary"
                                            alt="Fresh grapes" />
                                        <img src="{{ asset('img/best-product-3.jpg') }}" class="product-image product-image-secondary"
                                            alt="" aria-hidden="true" />
                                    </a>
                                    <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                            aria-hidden="true"></i> Add to Cart</a>
                                </div>
                                <div class="product-body">
                                    <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Seedless Grapes</a>
                                    <div class="product-meta">
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i>
                                        </div>
                                        <div class="product-price">
                                            <span class="product-price-current">$5.49</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <div class="product-card">
                                <span class="product-badge product-badge-new">New</span>
                                <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                                    <i class="far fa-heart" aria-hidden="true"></i>
                                </button>
                                <div class="product-media">
                                    <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Seedless Grapes">
                                        <img src="{{ asset('img/fruite-item-5.jpg') }}" class="product-image product-image-primary"
                                            alt="Fresh grapes" />
                                        <img src="{{ asset('img/best-product-3.jpg') }}" class="product-image product-image-secondary"
                                            alt="" aria-hidden="true" />
                                    </a>
                                    <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                            aria-hidden="true"></i> Add to Cart</a>
                                </div>
                                <div class="product-body">
                                    <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Seedless Grapes</a>
                                    <div class="product-meta">
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i>
                                        </div>
                                        <div class="product-price">
                                            <span class="product-price-current">$5.49</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <div class="product-card">
                                <span class="product-badge product-badge-new">New</span>
                                <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                                    <i class="far fa-heart" aria-hidden="true"></i>
                                </button>
                                <div class="product-media">
                                    <a href="{{ route('frontend.product-detail') }}" class="product-image-link"
                                        aria-label="View Seedless Grapes">
                                        <img src="{{ asset('img/fruite-item-5.jpg') }}" class="product-image product-image-primary"
                                            alt="Fresh grapes" />
                                        <img src="{{ asset('img/best-product-3.jpg') }}" class="product-image product-image-secondary"
                                            alt="" aria-hidden="true" />
                                    </a>
                                    <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                            aria-hidden="true"></i> Add to Cart</a>
                                </div>
                                <div class="product-body">
                                    <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Seedless Grapes</a>
                                    <div class="product-meta">
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i>
                                        </div>
                                        <div class="product-price">
                                            <span class="product-price-current">$5.49</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-4">
                            <div class="product-card">
                                <span class="product-badge product-badge-new">New</span>
                                <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                                    <i class="far fa-heart" aria-hidden="true"></i>
                                </button>
                                <div class="product-media">
                                    <a href="{{ route('frontend.product-detail') }}" class="product-image-link"
                                        aria-label="View Seedless Grapes">
                                        <img src="{{ asset('img/fruite-item-5.jpg') }}" class="product-image product-image-primary"
                                            alt="Fresh grapes" />
                                        <img src="{{ asset('img/best-product-3.jpg') }}" class="product-image product-image-secondary"
                                            alt="" aria-hidden="true" />
                                    </a>
                                    <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                            aria-hidden="true"></i> Add to Cart</a>
                                </div>
                                <div class="product-body">
                                    <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Seedless Grapes</a>
                                    <div class="product-meta">
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                class="fas fa-star"></i>
                                        </div>
                                        <div class="product-price">
                                            <span class="product-price-current">$5.49</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
