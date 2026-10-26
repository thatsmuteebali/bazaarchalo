@extends('layouts.app')
@section('customCss')
    <style>
        .order-detail-image {
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--bs-white);
        }

        .order-detail-item-info {
            min-width: 0;
            overflow-wrap: anywhere;
        }
    </style>
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
                    <h2 class="h3 mb-2">Order {{ $order->order_number }}</h2>
                    <p class="text-muted mb-0">Placed {{ $order->created_at->format('M j, Y g:i A') }}</p>
                </div>
                @php
                    $statusClass = match ($order->status) {
                        'completed' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        'processing' => 'bg-secondary text-dark',
                        default => 'bg-secondary text-dark',
                    };
                @endphp
                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="account-panel bg-light rounded p-4 p-md-5">
                        <h3 class="h5 mb-4">Order items</h3>
                        @foreach ($order->items as $item)
                            @php
                                $itemStatusClass = match ($item->status) {
                                    'accepted' => 'bg-success',
                                    'shipped' => 'bg-primary',
                                    'delivered' => 'bg-success',
                                    'rejected', 'cancelled' => 'bg-danger',
                                    default => 'bg-secondary text-dark',
                                };
                            @endphp
                            <div class="d-flex align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center gap-3 order-detail-item-info">
                                    <img src="{{ $item->image_url ?? asset('img/fruite-item-1.jpg') }}"
                                        class="order-detail-image" alt="{{ $item->product_name }}" loading="lazy" decoding="async">
                                    <div class="order-detail-item-info">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <h4 class="h6 mb-0">{{ $item->product_name }}</h4>
                                            <span class="order-item-status badge {{ $itemStatusClass }}">{{ ucfirst($item->status) }}</span>
                                        </div>
                                        @if ($item->variant_title)<small class="d-block text-muted">{{ $item->variant_title }}</small>@endif
                                        @if ($item->shop_name)<small class="d-block text-muted">{{ $item->shop_name }}</small>@endif
                                        <small class="text-muted">@money($item->unit_price) · Quantity {{ $item->quantity }}</small>
                                        @if ($item->status_reason)
                                            <small class="d-block text-danger mt-1">{{ $item->status_reason }}</small>
                                        @endif
                                    </div>
                                </div>
                                <strong class="text-nowrap">@money($item->line_total)</strong>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Subtotal</span><span>@money($order->subtotal)</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-3">
                            <span>Delivery</span><span>@if ($order->shipping_total > 0) @money($order->shipping_total) @else Free @endif</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-3">
                            <strong>Order total</strong><strong class="text-primary">@money($order->total)</strong>
                        </div>
                        @if ((float) $order->payable_total !== (float) $order->total)
                            <div class="d-flex justify-content-between mt-2">
                                <strong>Currently payable</strong><strong class="text-primary">@money($order->payable_total)</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 mb-4">
                        <h3 class="h5 mb-4">Delivery details</h3>
                        <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                        <p class="text-muted mb-3">{{ $order->customer_phone }}@if ($order->customer_email) · {{ $order->customer_email }}@endif</p>
                        <p class="mb-1"><strong>Address</strong></p>
                        <p class="text-muted mb-0">{{ $order->delivery_address }}</p>
                        @if ($order->notes)
                            <p class="mb-1 mt-3"><strong>Order notes</strong></p>
                            <p class="text-muted mb-0">{{ $order->notes }}</p>
                        @endif
                    </div>
                    <div class="bg-light rounded p-4 mb-4">
                        <h3 class="h5 mb-3">Payment</h3>
                        <p class="mb-2">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->online_payment_method) }}</p>
                        <p class="text-muted mb-0">{{ ucfirst($order->payment_status) }}</p>
                    </div>
                    <a href="{{ route('customer.order-tracking', ['order' => $order->order_number]) }}" class="btn btn-primary rounded-pill py-3 px-4 text-white w-100">
                        Track order <i class="fas fa-truck ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
