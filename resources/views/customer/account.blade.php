@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">My Account</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">My Account</li>
        </ol>
    </div>

    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')

                <div class="col-lg-8 col-xl-9">
                    <section>
                        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                            <div>
                                <p class="text-secondary fw-bold text-uppercase mb-2">Welcome back</p>
                                <h2 class="display-6 mb-0">Your account</h2>
                            </div>
                            <a href="{{ route('frontend.shop') }}"
                                class="btn btn-primary rounded-pill py-2 px-4 text-white">Shop fresh products
                                <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="account-stat bg-light rounded p-4">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>Orders placed</span>
                                    <strong>{{ $orderCount }}</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="account-stat bg-light rounded p-4">
                                    <i class="fas fa-heart"></i>
                                    <span>Wishlist items</span>
                                    <strong>{{ $wishlistCount }}</strong>
                                </div>
                            </div>
                            {{-- Reward points are not available yet; keep this card for future use.
                            <div class="col-md-4">
                                <div class="account-stat bg-light rounded p-4">
                                    <i class="fas fa-star"></i>
                                    <span>Reward points</span>
                                    <strong>260</strong>
                                </div>
                            </div>
                            --}}
                        </div>

                        <div class="account-panel bg-light rounded p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Recent orders</h4>
                                <a class="account-inline-tab text-primary" href="{{ route('customer.orders') }}">View all</a>
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
                                        @forelse ($recentOrders as $order)
                                            @php
                                                $itemQuantity = (int) ($order->items_sum_quantity ?? 0);
                                                $statusClass = match ($order->status) {
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'processing' => 'bg-secondary',
                                                    default => 'bg-secondary text-dark',
                                                };
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $order->order_number }}</strong>
                                                    <small class="d-block text-muted">{{ $itemQuantity }}
                                                        {{ $itemQuantity === 1 ? 'item' : 'items' }}</small>
                                                </td>
                                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                                <td>
                                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td><strong>@money($order->payable_total)</strong></td>
                                                <td>
                                                    <a href="{{ route('customer.order-detail', ['order' => $order->order_number]) }}"
                                                        class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="fas fa-box-open fa-2x text-secondary mb-3"
                                                        aria-hidden="true"></i>
                                                    <p class="text-muted mb-3">You haven’t placed an order yet.</p>
                                                    <a href="{{ route('frontend.shop') }}"
                                                        class="btn btn-primary rounded-pill px-4">Start shopping</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
