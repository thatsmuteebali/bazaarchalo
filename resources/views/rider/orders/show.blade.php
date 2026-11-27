@extends('layouts.rider')

@section('customCss')
    <style>
        .rider-order-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, .8fr);
            align-items: start;
            gap: 20px;
        }

        .rider-order-panel {
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
        }

        .rider-order-panel h2 {
            margin: 0 0 18px;
            font-size: 16px;
            font-weight: 700;
        }

        .rider-order-item {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: start;
            padding: 18px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .rider-order-item:first-child {
            padding-top: 0;
        }

        .rider-order-image,
        .rider-order-image-empty {
            display: grid;
            width: 64px;
            height: 64px;
            place-items: center;
            border-radius: 9px;
            object-fit: cover;
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 22px;
        }

        .rider-order-item-main {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .rider-order-item-title {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 650;
        }

        .rider-order-muted {
            color: #64748b;
            font-size: 13px;
        }

        .rider-order-item-total {
            white-space: nowrap;
            font-weight: 700;
        }

        .rider-order-item-actions {
            grid-column: 2 / -1;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 10px;
        }

        .rider-order-info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .rider-order-info-row:last-child {
            border-bottom: 0;
        }

        .rider-tracking-event {
            padding: 15px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .rider-tracking-event:last-child {
            border-bottom: 0;
        }

        @media (max-width: 767px) {
            .rider-order-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .rider-order-panel {
                padding: 16px;
            }

            .rider-order-item {
                grid-template-columns: 52px minmax(0, 1fr);
                gap: 11px;
            }

            .rider-order-image,
            .rider-order-image-empty {
                width: 52px;
                height: 52px;
            }

            .rider-order-item-total {
                grid-column: 2;
            }

            .rider-order-item-actions {
                grid-column: 2;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $orderStatusClass = match ($order->status) {
            'processing', 'completed' => 'success',
            'cancelled' => 'failed',
            default => 'pending',
        };
        $paymentStatusClass = match ($order->payment_status) {
            'paid' => 'success',
            'failed', 'refunded' => 'failed',
            default => 'pending',
        };
        $itemsSubtotal = $order->items->sum('line_total');
        $itemsQuantity = $order->items->sum('quantity');
    @endphp

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

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="rider-order-layout">
        <div>
            <section class="rider-order-panel mb-3">
                <h2>Items for delivery <span class="rider-order-muted">({{ $order->items->count() }})</span></h2>
                @forelse ($order->items as $item)
                    @php
                        $itemStatusClass = match ($item->status) {
                            'delivered' => 'success',
                            'shipped' => 'pending',
                            default => 'pending',
                        };
                    @endphp
                    <article class="rider-order-item">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" class="rider-order-image" alt="{{ $item->product_name }}"
                                loading="lazy">
                        @else
                            <div class="rider-order-image-empty" aria-hidden="true"><i class="bi bi-image"></i></div>
                        @endif

                        <div class="rider-order-item-main">
                            <h3 class="rider-order-item-title">{{ $item->product_name }}</h3>
                            <div class="rider-order-muted">
                                @if ($item->variant_title)
                                    {{ $item->variant_title }} ·
                                @endif
                                Quantity {{ $item->quantity }} · {{ \App\Support\Money::format($item->unit_price) }} each
                            </div>
                            @if ($item->sku)
                                <div class="rider-order-muted">SKU: {{ $item->sku }}</div>
                            @endif
                            <span class="badge-table {{ $itemStatusClass }} mt-2">{{ ucfirst($item->status) }}</span>
                        </div>

                        <div class="rider-order-item-total">{{ \App\Support\Money::format($item->line_total) }}</div>

                        @if ($item->status === 'accepted')
                            <div class="rider-order-item-actions">
                                <form method="POST" action="{{ route('rider.orders.items.status', [$order, $item]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="shipped">
                                    <button type="submit" class="btn-quick-action">
                                        <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        <span>Mark as picked up</span>
                                    </button>
                                </form>
                            </div>
                        @elseif ($item->status === 'shipped')
                            <div class="rider-order-item-actions">
                                <form method="POST" action="{{ route('rider.orders.items.status', [$order, $item]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="btn-quick-action">
                                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                                        <span>Mark as delivered</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="rider-order-muted mb-0">There are no accepted items to deliver for this order.</p>
                @endforelse
            </section>

            <section class="rider-order-panel">
                <h2>Delivery tracking</h2>
                @forelse ($order->tracking as $event)
                    <article class="rider-tracking-event">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <strong>{{ $event->title }}</strong>
                            <small class="rider-order-muted">{{ $event->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                        @if ($event->item)
                            <div class="rider-order-muted">{{ $event->item->product_name }}</div>
                        @endif
                        @if ($event->description)
                            <p class="mb-0 mt-2">{{ $event->description }}</p>
                        @endif
                        @if ($event->courier_name || $event->tracking_number)
                            <div class="rider-order-muted mt-1">
                                {{ $event->courier_name }}
                                @if ($event->tracking_number)
                                    · Tracking number: {{ $event->tracking_number }}
                                @endif
                            </div>
                        @endif
                        @if ($event->location)
                            <div class="rider-order-muted">{{ $event->location }}</div>
                        @endif
                    </article>
                @empty
                    <p class="rider-order-muted mb-0">No delivery updates yet.</p>
                @endforelse
            </section>
        </div>

        <aside>
            <section class="rider-order-panel mb-3">
                <h2>Customer and delivery location</h2>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Customer</span>
                    <strong>{{ $order->customer?->name ?? $order->customer_name }}</strong>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Phone</span>
                    <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                </div>
                @if ($order->customer_email)
                    <div class="rider-order-info-row">
                        <span class="rider-order-muted">Email</span>
                        <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>
                    </div>
                @endif
                <div class="mt-3">
                    <div class="rider-order-muted mb-1">Delivery address</div>
                    <div>{{ $order->delivery_address }}</div>
                </div>
                @if ($order->notes)
                    <div class="mt-3">
                        <div class="rider-order-muted mb-1">Customer notes</div>
                        <div>{{ $order->notes }}</div>
                    </div>
                @endif
                @php
                    $mapLocation = $order->latitude !== null && $order->longitude !== null
                        ? $order->latitude.','.$order->longitude
                        : $order->delivery_address;
                @endphp
                <iframe title="Customer delivery location" width="100%" height="220" class="mt-3" style="border:0"
                    loading="lazy"
                    src="https://maps.google.com/maps?q={{ urlencode($mapLocation) }}&z=15&output=embed">
                </iframe>
                <a class="btn btn-outline-primary mt-3"
                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapLocation) }}"
                    target="_blank" rel="noopener noreferrer">
                    Open directions in Google Maps
                </a>
            </section>

            <section class="rider-order-panel mb-3">
                <h2>Order pricing</h2>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Items quantity</span>
                    <strong>{{ $itemsQuantity }} {{ $itemsQuantity === 1 ? 'unit' : 'units' }}</strong>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Items subtotal</span>
                    <strong>{{ \App\Support\Money::format($itemsSubtotal) }}</strong>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Delivery</span>
                    <strong>{{ \App\Support\Money::format($order->shipping_total) }}</strong>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Original total</span>
                    <strong>{{ \App\Support\Money::format($order->total) }}</strong>
                </div>
                <div class="rider-order-info-row fw-semibold">
                    <span>Total payable</span>
                    <strong>{{ \App\Support\Money::format($order->payable_total) }}</strong>
                </div>
            </section>

            <section class="rider-order-panel">
                <h2>Payment</h2>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Method</span>
                    <strong>{{ $order->payment_method_label }}</strong>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Payment status</span>
                    <span class="badge-table {{ $paymentStatusClass }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="rider-order-info-row">
                    <span class="rider-order-muted">Order status</span>
                    <span class="badge-table {{ $orderStatusClass }}">{{ ucfirst($order->status) }}</span>
                </div>
                @if ($order->paid_at)
                    <div class="rider-order-info-row">
                        <span class="rider-order-muted">Paid at</span>
                        <strong>{{ $order->paid_at->format('M j, Y g:i A') }}</strong>
                    </div>
                @endif

                @if ($canMarkPaid)
                    <div class="alert alert-info mt-3 mb-2">
                        All deliverable items are marked delivered. Confirm after you collect the cash from the customer.
                    </div>
                    <form id="markOrderPaidForm" method="POST"
                        action="{{ route('rider.orders.payment.paid', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#confirmPaymentModal">
                            <i class="bi bi-cash-coin me-1" aria-hidden="true"></i>
                            Mark as paid
                        </button>
                    </form>

                    <div class="modal fade" id="confirmPaymentModal" tabindex="-1"
                        aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="modal-title fs-5" id="confirmPaymentModalLabel">
                                        Confirm cash collection
                                    </h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-2">Have you collected the cash payment from the customer?</p>
                                    <div class="d-flex justify-content-between align-items-center rounded bg-light p-3">
                                        <span class="text-muted">Amount to collect</span>
                                        <strong>{{ \App\Support\Money::format($order->payable_total) }}</strong>
                                    </div>
                                    <p class="text-muted small mt-3 mb-0">
                                        Confirm only after you have received the full amount.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" form="markOrderPaidForm" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                                        Yes, mark as paid
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && ! $order->isCashOnDelivery())
                    <p class="rider-order-muted mt-3 mb-0">
                        This is not a cash-on-delivery order. Payment is handled through its selected payment method.
                    </p>
                @endif
            </section>
        </aside>
    </div>
@endsection
