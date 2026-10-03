@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <!-- Page Header -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order Tracking</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('customer.order-success') }}">Order Success</a>
            </li>
            <li class="breadcrumb-item active text-white">Order Tracking</li>
        </ol>
    </div>

    <!-- Order Tracking Start -->
    <main class="container-fluid py-5 order-tracking-page">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 680px">
                <p class="text-secondary fw-bold text-uppercase mb-2">
                    Fresh delivery in progress
                </p>
                <h2 class="display-6 mb-3">Track your order</h2>
                <p class="mb-0">
                    Keep an eye on your order from the local shop to your doorstep.
                </p>
            </div>

            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-lg-7">
                    <div class="bg-light rounded p-4 p-md-5 h-100">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                            <div>
                                <p class="text-muted mb-1">Order number</p>
                                <h4 class="mb-0 text-primary">#BC-10482</h4>
                            </div>
                            <span class="badge bg-secondary text-dark rounded-pill px-3 py-2">Out for delivery</span>
                        </div>

                        <div class="tracking-timeline">
                            <div class="tracking-step completed">
                                <span class="tracking-icon"><i class="fas fa-check"></i></span>
                                <div>
                                    <h5 class="mb-1">Order confirmed</h5>
                                    <p class="mb-0 text-muted">
                                        Your order was received by Bazaar Chalo.
                                    </p>
                                    <small class="text-secondary">Today, 1:12 PM</small>
                                </div>
                            </div>
                            <div class="tracking-step completed">
                                <span class="tracking-icon"><i class="fas fa-store"></i></span>
                                <div>
                                    <h5 class="mb-1">Being prepared</h5>
                                    <p class="mb-0 text-muted">
                                        Green Basket is picking your fresh products.
                                    </p>
                                    <small class="text-secondary">Today, 1:35 PM</small>
                                </div>
                            </div>
                            <div class="tracking-step active">
                                <span class="tracking-icon"><i class="fas fa-truck"></i></span>
                                <div>
                                    <h5 class="mb-1">Out for delivery</h5>
                                    <p class="mb-0 text-muted">
                                        Your rider is on the way to your address.
                                    </p>
                                    <small class="text-secondary">Today, 3:48 PM</small>
                                </div>
                            </div>
                            <div class="tracking-step">
                                <span class="tracking-icon"><i class="fas fa-home"></i></span>
                                <div>
                                    <h5 class="mb-1">Delivered</h5>
                                    <p class="mb-0 text-muted">
                                        Estimated arrival today, 4:00 - 6:00 PM.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-light rounded p-4 p-md-5 mb-4">
                        <h4 class="mb-4">Track another order</h4>
                        <form action="{{ route('customer.order-tracking') }}" method="get">
                            <label for="order-number" class="form-label">Order number</label>
                            <div class="input-group mb-2">
                                <input id="order-number" name="order" type="text" class="form-control py-3"
                                    placeholder="e.g. BC-10482" value="BC-10482" required />
                                <button type="submit" class="btn btn-primary px-4 text-white" aria-label="Track order">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">Enter the order number from your confirmation email.</small>
                        </form>
                    </div>

                    <div class="bg-light rounded p-4 p-md-5">
                        <h4 class="mb-4">Delivery details</h4>
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-map-marker-alt text-secondary mt-1 me-3"></i>
                            <div>
                                <strong>Delivery address</strong>
                                <p class="mb-0 text-muted">
                                    1429 Netus Rd<br />New York, NY 48247
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-store text-secondary mt-1 me-3"></i>
                            <div>
                                <strong>Shop</strong>
                                <p class="mb-0 text-muted">
                                    Green Basket<br />Everyday fruits and vegetables
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-lg-9">
                    <div class="bg-light rounded p-4 p-md-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Order summary</h4>
                            <a href="{{ route('customer.order-success') }}" class="text-primary">View confirmation <i
                                    class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span>Fresh broccoli <small class="text-muted">x 2</small></span><strong>$138.00</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span>Farm potatoes <small class="text-muted">x 2</small></span><strong>$138.00</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span>Farm-fresh bananas
                                <small class="text-muted">x 2</small></span><strong>$138.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total paid</strong><strong class="text-primary">$414.00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Order Tracking End -->
@endsection

@section('customjs')
@endsection
