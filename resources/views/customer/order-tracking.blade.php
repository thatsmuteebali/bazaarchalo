@extends('layouts.app')
@section('customCss')
    <style>
        .order-product-image {
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--bs-white);
        }

        .order-product-info {
            min-width: 0;
            overflow-wrap: anywhere;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order Tracking</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">My orders</a></li>
            <li class="breadcrumb-item active text-white">Order Tracking</li>
        </ol>
    </div>

    <main class="container-fluid py-5 order-tracking-page">
        <div class="container py-4">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <section class="bg-light rounded p-4 p-md-5">
                        <form action="{{ route('customer.order-tracking') }}" method="get" class="mb-4">
                            <label for="order-number" class="form-label fw-semibold">Find one of your orders</label>
                            <div class="input-group">
                                <input id="order-number" name="order" type="search" class="form-control py-3"
                                    placeholder="Order number, e.g. BC-261026-12345" value="{{ request('order') }}" required>
                                <button type="submit" class="btn btn-primary px-4 text-white" aria-label="Track order">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>

                        @if ($orderNotFound)
                            <div class="alert alert-warning" role="alert">We couldn’t find that order in your account. Check the order number and try again.</div>
                        @elseif (! $order)
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-2x text-secondary mb-3" aria-hidden="true"></i>
                                <h2 class="h5">No orders to track yet</h2>
                                <p class="text-muted mb-0">Your placed orders and their updates will appear here.</p>
                            </div>
                        @else
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <p class="text-muted mb-1">Order number</p>
                                    <h2 class="h4 text-primary mb-1">{{ $order->order_number }}</h2>
                                    <small class="text-muted">Placed {{ $order->created_at->format('M j, Y g:i A') }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2">{{ ucfirst($order->status) }}</span>
                            </div>

                            <div class="tracking-timeline">
                                @forelse ($tracking as $event)
                                    <div class="tracking-step {{ $loop->last ? 'active' : 'completed' }}">
                                        <span class="tracking-icon">
                                            <i class="fas {{ $event->status === 'placed' ? 'fa-check' : ($event->status === 'shipped' ? 'fa-truck' : ($event->status === 'delivered' ? 'fa-home' : 'fa-store')) }}" aria-hidden="true"></i>
                                        </span>
                                        <div>
                                            <h3 class="h6 mb-1">{{ $event->title }}</h3>
                                            @if ($event->description)<p class="mb-1 text-muted">{{ $event->description }}</p>@endif
                                            @if ($event->courier_name || $event->tracking_number)
                                                <p class="small mb-1">{{ $event->courier_name }} {{ $event->tracking_number ? '· ' . $event->tracking_number : '' }}</p>
                                            @endif
                                            @if ($event->location)<p class="small text-muted mb-1">{{ $event->location }}</p>@endif
                                            <small class="text-secondary">{{ $event->created_at->format('M j, Y g:i A') }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">There are no tracking updates yet. Your order has been received and is waiting for seller confirmation.</p>
                                @endforelse
                            </div>
                        @endif
                    </section>
                </div>

                @if ($order)
                    <aside class="col-lg-4">
                        <section class="bg-light rounded p-4 mb-4">
                            <h2 class="h5 mb-3">Delivery details</h2>
                            <div class="d-flex align-items-start gap-3">
                                <i class="fas fa-map-marker-alt text-secondary mt-1" aria-hidden="true"></i>
                                <p class="mb-0 text-muted">{{ $order->delivery_address }}</p>
                            </div>
                            <p class="small text-muted mt-3 mb-0">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
                        </section>

                        <section class="bg-light rounded p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">Order summary</h2>
                                <a href="{{ route('customer.order-success', ['order' => $order->order_number]) }}" class="small">Confirmation</a>
                            </div>
                            @foreach ($order->items as $item)
                                <div class="d-flex align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-center gap-3 order-product-info">
                                        <img class="order-product-image" src="{{ $item->image_url ?? asset('img/fruite-item-1.jpg') }}"
                                            alt="{{ $item->product_name }}" loading="lazy" decoding="async">
                                        <span>{{ $item->product_name }} <small class="text-muted">× {{ $item->quantity }}</small>
                                            @if ($item->variant_title)<small class="d-block text-muted">{{ $item->variant_title }}</small>@endif
                                        </span>
                                    </div>
                                    <strong class="text-nowrap">@money($item->line_total)</strong>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>@money($order->subtotal)</span></div>
                            <div class="d-flex justify-content-between mb-3"><span>Delivery</span><span>@if ($order->shipping_total > 0) @money($order->shipping_total) @else Free @endif</span></div>
                            <div class="d-flex justify-content-between border-top pt-3"><strong>Total</strong><strong class="text-primary">@money($order->total)</strong></div>
                            <p class="small text-muted mt-3 mb-0">Payment: {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->online_payment_method) }} · {{ ucfirst($order->payment_status) }}</p>
                        </section>
                    </aside>
                @endif
            </div>
        </div>
    </main>
@endsection
