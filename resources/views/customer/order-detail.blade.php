@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order Detail</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">My Orders</a></li>
            <li class="breadcrumb-item active text-white">Order Detail</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                <div>
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Order history
                    </p>
                    <h2 class="display-6 mb-2">Order #BC-10482</h2>
                    <p class="text-muted mb-0">Placed today · Green Basket</p>
                </div>
                <span class="badge bg-secondary text-dark rounded-pill px-3 py-2">Out for delivery</span>
            </div>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="account-panel bg-light rounded p-4 p-md-5">
                        <h4 class="mb-4">Order items</h4>
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <img src="{{ asset('img/vegetable-item-2.jpg') }}" class="rounded-circle" width="70" height="70"
                                alt="Fresh broccoli" />
                            <div class="ms-3 flex-grow-1">
                                <h5 class="mb-1">Fresh broccoli</h5>
                                <small class="text-muted">$69.00 · Quantity 2</small>
                            </div>
                            <strong>$138.00</strong>
                        </div>
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <img src="{{ asset('img/vegetable-item-5.jpg') }}" class="rounded-circle" width="70" height="70"
                                alt="Farm potatoes" />
                            <div class="ms-3 flex-grow-1">
                                <h5 class="mb-1">Farm potatoes</h5>
                                <small class="text-muted">$69.00 · Quantity 2</small>
                            </div>
                            <strong>$138.00</strong>
                        </div>
                        <div class="d-flex align-items-center border-bottom pb-3 mb-4">
                            <img src="{{ asset('img/vegetable-item-3.png') }}" class="rounded-circle" width="70" height="70"
                                alt="Farm-fresh bananas" />
                            <div class="ms-3 flex-grow-1">
                                <h5 class="mb-1">Farm-fresh bananas</h5>
                                <small class="text-muted">$69.00 · Quantity 2</small>
                            </div>
                            <strong>$138.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total paid</strong><strong class="text-primary">$414.00</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 p-md-5 mb-4">
                        <h4 class="mb-4">Delivery details</h4>
                        <p class="mb-1"><strong>Address</strong></p>
                        <p class="text-muted">1429 Netus Rd<br />New York, NY 48247</p>
                        <p class="mb-1"><strong>Estimated delivery</strong></p>
                        <p class="text-muted mb-0">Today, 4:00 - 6:00 PM</p>
                    </div>
                    <a href="{{ route('customer.order-tracking') }}" class="btn btn-primary rounded-pill py-3 px-4 text-white w-100">Track
                        order <i class="fas fa-truck ms-2"></i></a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
