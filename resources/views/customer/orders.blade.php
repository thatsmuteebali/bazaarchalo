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
                        @if (session('status'))
                            <div class="alert alert-info" role="status">{{ session('status') }}</div>
                        @endif
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
                                    @forelse ($orders as $order)
                                        @php
                                            $itemQuantity = (int) ($order->items_sum_quantity ?? 0);
                                            $statusClass = match ($order->status) {
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'processing' => 'bg-info text-dark',
                                                default => 'bg-secondary text-dark',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $order->order_number }}</strong>
                                                <small class="d-block text-muted">{{ $itemQuantity }} {{ $itemQuantity === 1 ? 'item' : 'items' }}</small>
                                            </td>
                                            <td>{{ $order->created_at->format('M j, Y') }}</td>
                                            <td><span class="badge {{ $statusClass }} rounded-pill px-3 py-2">{{ ucfirst($order->status) }}</span></td>
                                            <td><strong>@money($order->payable_total)</strong></td>
                                            <td>
                                                <a href="{{ route('customer.order-detail', ['order' => $order->order_number]) }}"
                                                    class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="fas fa-box-open fa-2x text-secondary mb-3" aria-hidden="true"></i>
                                                <p class="text-muted mb-3">You haven’t placed an order yet.</p>
                                                <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill px-4">Start shopping</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($orders->hasPages())
                            <div class="mt-4">{{ $orders->links() }}</div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
