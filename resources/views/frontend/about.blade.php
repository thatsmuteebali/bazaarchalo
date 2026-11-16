@extends('layouts.app')
@section('customCss')
    <style>
        .page-header {
            position: relative !important;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('storage/' . ltrim($about_page_content['Banner Background Image'], '/')) }}') !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{$about_page_content['Banner Title']}}</h1>
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
                        <img src="{{ asset('storage/'.ltrim($about_page_content['About Section Left Image'], '/')) }}" class="img-fluid rounded about-story-image"
                            alt="About Main Section Image" />
                    </div>
                    <div class="col-lg-6">
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            {{$about_page_content['About Section Subtitle']}}
                        </p>
                        <h2 class="display-6 mb-4">{{$about_page_content['About Section Title']}}</h2>
                        <div class="mb-4">
                            {!! $about_page_content['About Section Descripion'] !!}
                        </div>
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
                        {{$about_page_content['Guide Section Subtitle']}}
                    </p>
                    <h2 class="display-6 mb-3">{{$about_page_content['Guide Section Title']}}</h2>
                    <p class="mb-0">
                        {{$about_page_content['Guide Section Description']}}
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="{{$about_page_content['Guide Section Step1 icon']}} fa-2x text-white"></i>
                            </div>
                            <h5>{{$about_page_content['Guide Section Step1 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['Guide Section Step1 Description']}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="{{$about_page_content['Guide Section Step2 icon']}} fa-2x text-white"></i>
                            </div>
                            <h5>{{$about_page_content['Guide Section Step2 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['Guide Section Step2 Description']}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="about-value text-center bg-white rounded p-4 h-100">
                            <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4">
                                <i class="{{$about_page_content['Guide Section Step3 icon']}} fa-2x text-white"></i>
                            </div>
                            <h5>{{$about_page_content['Guide Section Step3 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['Guide Section Step3 Description']}}
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
                        {{$about_page_content['How Bazaar Chalo Subtitle']}}
                    </p>
                    <h2 class="display-6 mb-3">{{$about_page_content['How Bazaar Chalo Title']}}</h2>
                    <p class="mb-0">
                        {{$about_page_content['How Bazaar Chalo Description']}}
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">01</span><i class="{{$about_page_content['How Bazaar Chalo step1 Icon']}}"></i>
                            <h5>{{$about_page_content['How Bazaar Chalo step1 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['How Bazaar Chalo step1 Description']}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">02</span><i class="{{$about_page_content['How Bazaar Chalo step2 Icon']}}"></i>
                            <h5>{{$about_page_content['How Bazaar Chalo step2 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['How Bazaar Chalo step2 Description']}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <span class="process-number">03</span><i class="{{$about_page_content['How Bazaar Chalo step3 Icon']}}"></i>
                            <h5>{{$about_page_content['How Bazaar Chalo step3 Title']}}</h5>
                            <p class="mb-0">
                                {{$about_page_content['How Bazaar Chalo step3 Description']}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- How It Works End -->

        <!-- Community CTA Start -->
        {{-- <section class="container-fluid py-5 seller-section">
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
        </section> --}}
        <!-- Community CTA End -->
    </main>
@endsection
