@extends('layouts.app')
@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">About Bazaar Chalo</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">About Us</li>
        </ol>
    </div>

    <main>
        <!-- Story Start -->
        <section class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid rounded about-story-image"
                            alt="Fresh produce from a local Bazaar Chalo seller" />
                    </div>
                    <div class="col-lg-6">
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Fresh shopping, local impact
                        </p>
                        <h2 class="display-6 mb-4">Your local bazaar, brought closer</h2>
                        <p class="mb-3">
                            Bazaar Chalo makes it simple to discover fresh products from
                            trusted shops in your neighbourhood. We bring the warmth of
                            local shopping to a convenient online experience.
                        </p>
                        <p class="mb-4">
                            From everyday fruits and vegetables to pantry essentials, every
                            order helps local sellers reach more customers while giving you
                            quality products and friendly service.
                        </p>
                        <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill py-3 px-5 text-white">Explore local
                            shops <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Story End -->

        <!-- Values Start -->
        <section class="container-fluid py-5 bg-light">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5" style="max-width: 700px">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        What guides us
                    </p>
                    <h2 class="display-6 mb-3">Built around the neighbourhood</h2>
                    <p class="mb-0">
                        We believe good food, good service, and strong communities belong
                        together.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="fas fa-leaf fa-2x text-white"></i>
                            </div>
                            <h5>Fresh and dependable</h5>
                            <p class="mb-0">
                                We help you find quality products selected by sellers who know
                                their community.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="fas fa-store fa-2x text-white"></i>
                            </div>
                            <h5>Local sellers first</h5>
                            <p class="mb-0">
                                Our marketplace gives neighbourhood shops a simple way to grow
                                online.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="fas fa-heart fa-2x text-white"></i>
                            </div>
                            <h5>Shopping with purpose</h5>
                            <p class="mb-0">
                                Every order keeps more value close to home and supports the
                                people around you.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Values End -->

        <!-- How It Works Start -->
        <section class="container-fluid py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5" style="max-width: 700px">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Simple by design
                    </p>
                    <h2 class="display-6 mb-3">How Bazaar Chalo works</h2>
                    <p class="mb-0">
                        A better local shopping journey in three easy steps.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">01</span><i class="fas fa-search-location"></i>
                            <h5>Find a nearby shop</h5>
                            <p class="mb-0">
                                Discover trusted sellers and fresh products close to you.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">02</span><i class="fas fa-shopping-basket"></i>
                            <h5>Choose what you need</h5>
                            <p class="mb-0">
                                Build your basket with quality products from local stores.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">03</span><i class="fas fa-truck"></i>
                            <h5>Enjoy easy delivery</h5>
                            <p class="mb-0">
                                Get your order delivered with care, right to your doorstep.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- How It Works End -->

        <!-- Community CTA Start -->
        <section class="container-fluid py-5 seller-section">
            <div class="container py-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Be part of the bazaar
                        </p>
                        <h2 class="display-6 mb-3">Let’s help local businesses thrive</h2>
                        <p class="lead mb-0">
                            Whether you are shopping for your family or growing your shop,
                            Bazaar Chalo is here to make local commerce easier for everyone.
                        </p>
                    </div>
                    <div class="col-xxl-4 text-xxl-end">
                        <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill py-3 px-5 text-white me-2">Start
                            shopping</a><a href="{{ route('register') }}"
                            class="btn border border-secondary rounded-pill py-3 px-4 text-primary">Become a seller</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Community CTA End -->
    </main>
@endsection
