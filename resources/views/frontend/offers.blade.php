@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Fresh Offers</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Offers</li>
        </ol>
    </div>

    <main>
        <section class="container-fluid offers-intro py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <span class="text-secondary fw-bold text-uppercase">Limited time savings</span>
                        <h1 class="display-4 text-primary mt-2 mb-4">
                            Good food, better prices.
                        </h1>
                        <p class="lead mb-4">
                            Fill your basket with fresh fruits and vegetables while these
                            seasonal offers last.
                        </p>
                        <a href="#deals"
                            class="btn btn-primary border-2 border-secondary rounded-pill text-white py-3 px-5">Shop
                            today's deals</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="offers-hero-image position-relative">
                            <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid w-100 rounded" alt="Fresh fruit offer" />
                            <div
                                class="offers-hero-badge bg-secondary rounded-circle d-flex flex-column align-items-center justify-content-center">
                                <span class="small text-dark">UP TO</span><strong
                                    class="display-5 text-dark">30%</strong><span class="small text-dark">OFF</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container-fluid py-5">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h4 class="text-primary">Shop and save</h4>
                    <h1 class="display-5">Offers for every basket</h1>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="offer-tile bg-primary rounded p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-white-50">Fresh fruit favourites</span>
                                    <h2 class="text-white mt-2">20% OFF</h2>
                                    <p class="text-white mb-4">On selected seasonal fruits.</p>
                                </div>
                                <i class="fas fa-apple-alt text-secondary fa-2x"></i>
                            </div>
                            <a href="#deals" class="btn bg-white text-primary rounded-pill px-4">Explore fruits</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="offer-tile bg-secondary rounded p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-dark-50">Everyday vegetables</span>
                                    <h2 class="text-dark mt-2">15% OFF</h2>
                                    <p class="text-dark mb-4">On fresh greens and roots.</p>
                                </div>
                                <i class="fas fa-carrot text-primary fa-2x"></i>
                            </div>
                            <a href="#deals" class="btn bg-white text-primary rounded-pill px-4">Explore vegetables</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="offer-tile bg-dark rounded p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-white-50">Your first basket</span>
                                    <h2 class="text-secondary mt-2">FREE DELIVERY</h2>
                                    <p class="text-white mb-4">On orders over $300.</p>
                                </div>
                                <i class="fas fa-truck text-secondary fa-2x"></i>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn bg-white text-primary rounded-pill px-4">Start shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection

@section('customjs')
@endsection
