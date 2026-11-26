@extends('layouts.rider')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Order {{ $order->order_number }}</h1>
            <p class="page-subtitle">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
        </div>
        <a href="{{ route('rider.orders.index') }}" class="btn-quick-action">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>All orders</span>
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-light shadow-sm p-4">
                <h2 class="h5 mb-3">Accepted order items</h2>
                @forelse ($order->items as $item)
                    @php
                        $statusClass = match ($item->status) {
                            'delivered' => 'success',
                            'shipped' => 'primary',
                            default => 'warning text-dark',
                        };
                    @endphp
                    <article class="d-flex flex-wrap align-items-center gap-3 border-bottom py-3">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}"
                                class="rounded" width="72" height="72" style="object-fit: cover;">
                        @else
                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                style="width: 72px; height: 72px;">
                                <i class="bi bi-image text-muted" aria-hidden="true"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h3 class="h6 mb-1">{{ $item->product_name }}</h3>
                            <p class="small text-muted mb-1">
                                {{ $item->shop_name ?? 'Seller shop' }} · Quantity {{ $item->quantity }}
                            </p>
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                        </div>
                        @if ($item->status === 'accepted')
                            <form method="POST"
                                action="{{ route('rider.orders.items.status', [$order, $item]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="btn btn-primary">Mark as picked up</button>
                            </form>
                        @elseif ($item->status === 'shipped')
                            <form method="POST"
                                action="{{ route('rider.orders.items.status', [$order, $item]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-success">Mark as delivered</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <p class="text-muted mb-0">There are no accepted items to deliver for this order.</p>
                @endforelse
            </section>

            <section class="card border-light shadow-sm p-4 mt-4">
                <h2 class="h5 mb-3">Delivery progress</h2>
                @forelse ($order->tracking as $event)
                    <article class="border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <strong>{{ $event->title }}</strong>
                            <small class="text-muted">{{ $event->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                        @if ($event->item)
                            <div class="text-muted small">{{ $event->item->product_name }}</div>
                        @endif
                        @if ($event->description)
                            <p class="mb-0 mt-2">{{ $event->description }}</p>
                        @endif
                    </article>
                @empty
                    <p class="text-muted mb-0">No delivery updates yet.</p>
                @endforelse
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card border-light shadow-sm p-4">
                <h2 class="h5 mb-3">Customer and delivery location</h2>
                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                <p class="mb-2"><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                @if ($order->customer_email)
                    <p class="mb-3"><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></p>
                @endif
                <p class="mb-2"><strong>Address</strong><br>{{ $order->delivery_address }}</p>
                @if ($order->notes)
                    <p class="mb-3"><strong>Customer notes</strong><br>{{ $order->notes }}</p>
                @endif
                @php
                    $mapLocation = $order->latitude !== null && $order->longitude !== null
                        ? $order->latitude.','.$order->longitude
                        : $order->delivery_address;
                @endphp
                <iframe title="Customer delivery location" width="100%" height="260" style="border:0"
                    loading="lazy"
                    src="https://maps.google.com/maps?q={{ urlencode($mapLocation) }}&z=15&output=embed">
                </iframe>
                <a class="btn btn-outline-primary mt-3"
                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapLocation) }}"
                    target="_blank" rel="noopener noreferrer">
                    Open directions in Google Maps
                </a>
            </section>
        </div>
    </div>
@endsection
