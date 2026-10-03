@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <header class="container-fluid page-header py-5">
        <div class="container text-center py-5">
            <p class="text-secondary fw-bold text-uppercase mb-2">
                Fresh from your neighbourhood
            </p>
            <h1 class="text-white display-5 mb-3">Popular Shops Near You</h1>
            <p class="text-white mb-0">
                Meet the local sellers behind the products you love.
            </p>
            <ol class="breadcrumb justify-content-center mt-4 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Shops</li>
            </ol>
        </div>
    </header>

    <main>
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Your local marketplace
                        </p>
                        <h2 class="display-6 mb-2">Find a shop you can trust</h2>
                        <p class="text-muted mb-0">
                            Fresh produce and pantry essentials, delivered from sellers near
                            you.
                        </p>
                    </div>
                    <span class="shop-result-count mt-3 mt-lg-0"><i
                            class="fas fa-map-marker-alt text-secondary me-2"></i>Showing
                        shops near New York</span>
                </div>
                <div class="shop-directory-toolbar bg-light rounded p-3 mb-5">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0"><i
                                        class="fas fa-search text-primary"></i></span><input type="search"
                                    class="form-control border-0 py-3" placeholder="Search by shop name or speciality"
                                    aria-label="Search shops" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <select class="form-select border-0 py-3" aria-label="Filter shops by delivery">
                                <option>All delivery options</option>
                                <option>Free delivery</option>
                                <option>Same-day delivery</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <select class="form-select border-0 py-3" aria-label="Sort shops">
                                <option>Recommended</option>
                                <option>Highest rated</option>
                                <option>Nearest first</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-1.jpg') }}" alt="Green Basket"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">Green Basket</h4>
                            <p class="text-muted mb-3">
                                Everyday fruits and vegetables, picked fresh from nearby
                                farms.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.9
                                    <small>(128)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>1.2 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Fresh produce</span><span>Organic</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-2.jpg') }}" alt="Daily Harvest"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">Daily Harvest</h4>
                            <p class="text-muted mb-3">
                                Local produce and pantry essentials for the whole family.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.8
                                    <small>(94)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>2.4 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Pantry</span><span>Same-day delivery</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-3.jpg') }}" alt="Nature's Store"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">Nature's Store</h4>
                            <p class="text-muted mb-3">
                                Organic choices and seasonal favourites from local sellers.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.7
                                    <small>(76)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>3.1 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Organic</span><span>Seasonal</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-4.jpg') }}" alt="The Green Crate"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">The Green Crate</h4>
                            <p class="text-muted mb-3">
                                Colourful vegetables and weekly boxes for simple healthy
                                meals.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.8
                                    <small>(61)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>3.8 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Weekly boxes</span><span>Vegetables</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-5.jpg') }}" alt="Harvest Corner"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">Harvest Corner</h4>
                            <p class="text-muted mb-3">
                                A friendly neighbourhood grocer for everyday essentials.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.6
                                    <small>(49)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>4.2 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Groceries</span><span>Free delivery</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="seller-directory-card h-100">
                            <div class="seller-directory-head">
                                <img src="{{ asset('img/best-product-6.jpg') }}" alt="Market & More"
                                    class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                        class="fas fa-circle"></i> Open now</span>
                            </div>
                            <h4 class="mt-4 mb-2">Market & More</h4>
                            <p class="text-muted mb-3">
                                A curated mix of fresh goods, treats, and local favourites.
                            </p>
                            <div class="seller-meta">
                                <span><i class="fas fa-star text-secondary me-1"></i>4.7
                                    <small>(83)</small></span><span><i
                                        class="fas fa-map-marker-alt text-secondary me-1"></i>4.8 km</span>
                            </div>
                            <div class="seller-tags">
                                <span>Local favourites</span><span>Fresh goods</span>
                            </div>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                        </article>
                    </div>
                </div>
            </div>
        </section>
        <section class="container-fluid py-5 bg-light">
            <div class="container py-4">
                <div
                    class="directory-banner rounded p-4 p-lg-5 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Are you a local seller?
                        </p>
                        <h2 class="mb-2">Bring your shop online.</h2>
                        <p class="mb-0 text-muted">
                            Reach more nearby customers and grow with Bazaar Chalo.
                        </p>
                    </div>
                    <a href="{{ route('seller.register') }}" class="btn btn-primary rounded-pill px-4 mt-4 mt-lg-0">Register your shop <i
                            class="fas fa-store ms-2"></i></a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
