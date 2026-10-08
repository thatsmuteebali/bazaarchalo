@extends('layouts.app')
@section('content')
    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7" data-aos="fade-right" data-aos-duration="900">
                    <h4 class="mb-3 text-secondary">Sell your local products</h4>
                    <h1 class="mb-5 display-3 text-primary">
                        Apne Bazaar Se Apne Ghar Tak
                    </h1>
                    <form action="{{ route('frontend.shop') }}" method="GET" class="position-relative mx-auto">
                        <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="search"
                            name="q" placeholder="Search for a product..." aria-label="Search for a product" />
                        <button type="submit"
                            class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                            style="top: 0; right: 25%">
                            Search
                        </button>
                    </form>
                </div>
                <div class="col-md-12 col-lg-5" data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active rounded">
                                <img src="{{ asset('img/hero-img-1.png') }}" class="img-fluid w-100 h-100 bg-secondary rounded"
                                    alt="First slide" />
                                <a href="#" class="btn px-4 py-2 text-white rounded">Fruites</a>
                            </div>
                            <div class="carousel-item rounded">
                                <img src="{{ asset('img/hero-img-2.jpg') }}" class="img-fluid w-100 h-100 rounded" alt="Second slide" />
                                <a href="#" class="btn px-4 py-2 text-white rounded">Vesitables</a>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <main>

        <!-- Popular Category Start -->
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Shop by collection
                        </p>
                        <h2 class="display-6 mb-0">Popular Categories</h2>
                    </div>
                    <a href="{{ route('frontend.categories') }}" class="btn border border-secondary rounded-pill px-4 text-primary">View
                        all
                        categories <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="owl-carousel category-carousel" data-aos="fade-up" data-aos-delay="100">
                    @if (count($categories) > 0)
                        @foreach ($categories as $category)
                            <a href="{{ route('frontend.shop', ['category' => $category->id]) }}" class="category-card">
                                <img src="{{ $category->image ? $category->image_url : asset('img/fruite-item-1.jpg') }}" alt="{{ $category->name }}" />
                                <span>{{$category->name}}</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
        <!-- Popular Category End -->

        <!-- Popular Shops Near You Start -->
        <section class="container-fluid py-5 bg-light bazaar-section">
            <div class="container py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Fresh from your neighbourhood
                        </p>
                        <h2 class="display-6 mb-0">Popular Shops Near You</h2>
                    </div>
                    <a href="{{ route('frontend.shop-listing') }}" class="btn border border-secondary rounded-pill px-4 text-primary">Explore
                        shops
                        <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="row g-4">
                    @if (count($shops) > 0)
                        @foreach ($shops as $shop)
                            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="shop-card bg-white rounded p-4 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $shop->banner_url }}" alt="Green Basket"
                                            class="shop-avatar rounded-circle me-3" />
                                        <div>
                                            <h5 class="mb-1">{{$shop->name}}</h5>
                                            {{-- <small class="text-muted"><i class="fas fa-map-marker-alt text-secondary me-1"></i>
                                                1.2 km away</small> --}}
                                        </div>
                                    </div>
                                    <p class="mb-3">
                                        {{$shop->description}}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary"><i class="fas fa-star text-secondary me-1"></i> 4.9</span><a
                                            href="{{ route('frontend.shop', ['shop' => $shop->id]) }}"
                                            class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">Visit
                                            shop</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
        <!-- Popular Shops Near You End -->

        <!-- Popular Products Start -->
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Handpicked for you
                        </p>
                        <h2 class="display-6 mb-0">Popular Products</h2>
                    </div>
                    <a href="{{ route('frontend.shop') }}" class="btn border border-secondary rounded-pill px-4 text-primary">Shop all
                        products <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="owl-carousel product-carousel" data-aos="fade-up" data-aos-delay="100">
                    @forelse ($featuredProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                    @empty
                        <div class="text-muted py-4">No featured products are available right now.</div>
                    @endforelse
                </div>
            </div>
        </section>
        <!-- Popular Products End -->

        <!-- Today's Deals / Offers Start -->
        <section class="container-fluid py-5 bg-light bazaar-section">
            <div class="container py-4">
                <div class="text-center mx-auto mb-5" style="max-width: 700px" data-aos="fade-up">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Save more today
                    </p>
                    <h2 class="display-6">Today's Deals / Offers</h2>
                    <p class="mb-0">
                        Good food, better value. Discover offers from trusted local
                        sellers.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                        <a href="{{ route('frontend.offers') }}" class="offer-card d-block rounded overflow-hidden position-relative"><img
                                src="{{ asset('img/featur-1.jpg') }}" class="img-fluid w-100" alt="Fresh apples offer" />
                            <div class="offer-card-content">
                                <span>Fresh Apples</span><strong>20% OFF</strong><small>Shop today's offer</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                        <a href="{{ route('frontend.offers') }}" class="offer-card d-block rounded overflow-hidden position-relative"><img
                                src="{{ asset('img/featur-2.jpg') }}" class="img-fluid w-100" alt="Free delivery offer" />
                            <div class="offer-card-content">
                                <span>Fresh on your doorstep</span><strong>Free delivery</strong><small>On orders over
                                    $30</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                        <a href="{{ route('frontend.offers') }}" class="offer-card d-block rounded overflow-hidden position-relative"><img
                                src="{{ asset('img/featur-3.jpg') }}" class="img-fluid w-100" alt="Vegetable discount offer" />
                            <div class="offer-card-content">
                                <span>Seasonal vegetables</span><strong>Up to 30% OFF</strong><small>Limited-time
                                    savings</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Today's Deals / Offers End -->

        <!-- How Bazaar Chalo Works Start -->
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="text-center mx-auto mb-5" style="max-width: 700px" data-aos="fade-up">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Simple shopping, local impact
                    </p>
                    <h2 class="display-6">How Bazaar Chalo Works</h2>
                    <p class="mb-0">
                        From a nearby shop to your doorstep in three easy steps.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="process-step text-center">
                            <span class="process-number">01</span><i class="fas fa-search-location"></i>
                            <h5>Find local shops</h5>
                            <p class="mb-0">
                                Browse trusted sellers and discover what is fresh near you.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="process-step text-center">
                            <span class="process-number">02</span><i class="fas fa-shopping-basket"></i>
                            <h5>Choose your favourites</h5>
                            <p class="mb-0">
                                Add quality products from your favourite local stores.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="process-step text-center">
                            <span class="process-number">03</span><i class="fas fa-truck"></i>
                            <h5>Get it delivered</h5>
                            <p class="mb-0">
                                Enjoy a smooth delivery and support businesses around you.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- How Bazaar Chalo Works End -->

        <!-- Become a Seller Start -->
        <section class="container-fluid py-5 seller-section">
            <div class="container py-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7" data-aos="fade-right">
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Grow with your community
                        </p>
                        <h2 class="display-6 mb-3">Become a Seller on Bazaar Chalo</h2>
                        <p class="lead mb-4">
                            Take your shop online, reach more local customers, and manage
                            your products and orders in one simple place.
                        </p>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <strong class="d-block"><i class="fas fa-store text-secondary me-2"></i>List your
                                    shop</strong><small>Set up your storefront</small>
                            </div>
                            <div class="col-sm-4">
                                <strong class="d-block"><i class="fas fa-box-open text-secondary me-2"></i>Add
                                    products</strong><small>Showcase your stock</small>
                            </div>
                            <div class="col-sm-4">
                                <strong class="d-block"><i class="fas fa-chart-line text-secondary me-2"></i>Grow
                                    sales</strong><small>Reach local buyers</small>
                            </div>
                        </div>
                        <a href="{{ route('seller.register') }}" class="btn btn-primary rounded-pill py-3 px-5">Register your shop <i
                                class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                    <div class="col-lg-5" data-aos="fade-left" data-aos-delay="150">
                        <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid rounded seller-image"
                            alt="Fresh produce for local shops" />
                    </div>
                </div>
            </div>
        </section>
        <!-- Become a Seller End -->

        <!-- Why Bazaar Chalo Start -->
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="text-center mx-auto mb-5" style="max-width: 700px" data-aos="fade-up">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Why choose us
                    </p>
                    <h2 class="display-6">Why Bazaar Chalo</h2>
                    <p class="mb-0">
                        A better way to shop local and help neighbourhood sellers thrive.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                        <div class="why-card text-center p-4">
                            <i class="fas fa-leaf"></i>
                            <h5>Fresh and local</h5>
                            <p class="mb-0">
                                Shop produce sourced from sellers close to you.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                        <div class="why-card text-center p-4">
                            <i class="fas fa-shield-alt"></i>
                            <h5>Trusted sellers</h5>
                            <p class="mb-0">
                                Buy with confidence from verified local shops.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
                        <div class="why-card text-center p-4">
                            <i class="fas fa-hand-holding-heart"></i>
                            <h5>Support local</h5>
                            <p class="mb-0">Every order helps a small business grow.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="400">
                        <div class="why-card text-center p-4">
                            <i class="fas fa-shipping-fast"></i>
                            <h5>Easy delivery</h5>
                            <p class="mb-0">
                                Get your everyday essentials delivered with ease.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Why Bazaar Chalo End -->

    </main>
@endsection
