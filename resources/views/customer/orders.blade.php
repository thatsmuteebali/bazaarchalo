@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">My Orders</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.account') }}">My Account</a></li>
            <li class="breadcrumb-item active text-white">My Orders</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')
                <section class="col-lg-8 col-xl-9">
                    <div class="account-panel bg-light rounded p-4 p-md-5">
                        <div class="mb-4">
                            <p class="text-secondary fw-bold text-uppercase mb-2">
                                Your shopping history
                            </p>
                            <h2 class="display-6 mb-2">My Orders</h2>
                            <p class="text-muted mb-0">
                                View and track all your Bazaar Chalo orders.
                            </p>
                        </div>
                        <div class="table-responsive">
                            <table class="table account-orders-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>#BC-10482</strong><small class="d-block text-muted">3 items</small>
                                        </td>
                                        <td>Today</td>
                                        <td>
                                            <span class="badge bg-secondary text-dark rounded-pill px-3 py-2">Out for
                                                delivery</span>
                                        </td>
                                        <td><strong>$414.00</strong></td>
                                        <td>
                                            <a href="{{ route('customer.order-detail') }}"
                                                class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>#BC-10371</strong><small class="d-block text-muted">5 items</small>
                                        </td>
                                        <td>18 Aug 2026</td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill px-3 py-2">Delivered</span>
                                        </td>
                                        <td><strong>$86.50</strong></td>
                                        <td>
                                            <a href="{{ route('customer.order-detail') }}"
                                                class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>#BC-10192</strong><small class="d-block text-muted">2 items</small>
                                        </td>
                                        <td>04 Aug 2026</td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill px-3 py-2">Delivered</span>
                                        </td>
                                        <td><strong>$42.75</strong></td>
                                        <td>
                                            <a href="{{ route('customer.order-detail') }}"
                                                class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
