@extends('layouts.app')
@section('customCss')
    <style>
        .order-product-image {
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
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
        <h1 class="text-center text-white display-6">Order confirmed</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">My orders</a></li>
            <li class="breadcrumb-item active text-white">Confirmation</li>
        </ol>
    </div>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <section class="bg-light rounded p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-4" style="width: 76px; height: 76px">
                            <i class="fas fa-check fa-2x text-white"></i>
                        </div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">Thank you for shopping local</p>
                        <h1 class="h2 mb-2">Your order has been placed</h1>
                        <p class="text-muted mb-0">We’ll update your order timeline as sellers prepare your items.</p>
                    </div>

                    <div class="bg-white rounded p-4 mb-4">
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3 gap-3">
                            <span class="text-muted">Order number</span>
                            <strong class="text-primary">{{ $order->order_number }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3 gap-3">
                            <span class="text-muted">Placed</span>
                            <strong>{{ $order->created_at->format('M j, Y g:i A') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3 gap-3">
                            <span class="text-muted">Payment</span>
                            <strong>
                                {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->online_payment_method) }}
                                <span class="text-muted fw-normal">({{ ucfirst($order->payment_status) }})</span>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between gap-3">
                            <span class="text-muted">Order total</span>
                            <strong class="text-primary">@money($order->total)</strong>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Items in this order</h2>
                    <div class="bg-white rounded p-4 mb-4">
                        @foreach ($order->items as $item)
                            <div class="d-flex align-items-center justify-content-between gap-3 {{ $loop->last ? '' : 'border-bottom pb-3 mb-3' }}">
                                <div class="d-flex align-items-center gap-3 order-product-info">
                                    <img class="order-product-image" src="{{ $item->image_url ?? asset('img/fruite-item-1.jpg') }}"
                                        alt="{{ $item->product_name }}" loading="lazy" decoding="async">
                                    <div class="order-product-info">
                                        <strong>{{ $item->product_name }}</strong>
                                        @if ($item->variant_title)<small class="d-block text-muted">{{ $item->variant_title }}</small>@endif
                                        <small class="d-block text-muted">{{ $item->quantity }} × @money($item->unit_price)</small>
                                    </div>
                                </div>
                                <strong class="text-nowrap">@money($item->line_total)</strong>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between border-top pt-3 mt-3">
                            <span>Delivery</span><strong>@if ($order->shipping_total > 0) @money($order->shipping_total) @else Free @endif</strong>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="{{ route('customer.order-tracking', ['order' => $order->order_number]) }}" class="btn btn-primary rounded-pill py-3 px-4 text-white">
                            Track this order <i class="fas fa-truck ms-2"></i>
                        </a>
                        <a href="{{ route('frontend.shop') }}" class="btn border border-secondary rounded-pill py-3 px-4 text-primary">
                            Continue shopping <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection
