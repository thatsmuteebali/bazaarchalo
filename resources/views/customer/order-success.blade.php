@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order Success</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.checkout') }}">Checkout</a></li>
            <li class="breadcrumb-item active text-white">Order Success</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Order Success Start -->
    <main class="container-fluid py-5">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="bg-light rounded p-4 p-md-5 text-center">
                        <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4"
                            style="width: 90px; height: 90px">
                            <i class="fas fa-check fa-2x text-white"></i>
                        </div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Thank you for shopping local
                        </p>
                        <h1 class="display-6 mb-3">Your order has been placed!</h1>
                        <p class="mb-4">
                            We have received your order and will start preparing your fresh
                            products shortly.
                        </p>

                        <div class="bg-white rounded p-4 mb-4 text-start">
                            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                                <span class="text-muted">Order number</span>
                                <strong class="text-primary">#BC-10482</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                                <span class="text-muted">Estimated delivery</span>
                                <strong>Today, 4:00 - 6:00 PM</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total paid</span>
                                <strong class="text-primary">$414.00</strong>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill py-3 px-4 text-white">Continue
                                shopping <i class="fas fa-arrow-right ms-2"></i></a>
                            <a href="{{ route('customer.order-tracking') }}"
                                class="btn border border-secondary rounded-pill py-3 px-4 text-primary">Track order <i
                                    class="fas fa-truck ms-2"></i></a>
                            <a href="{{ route('frontend.home') }}"
                                class="btn border border-secondary rounded-pill py-3 px-4 text-primary">Back to home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Order Success End -->
@endsection

@section('customjs')
@endsection
