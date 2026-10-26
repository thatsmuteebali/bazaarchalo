@extends('layouts.seller')

@section('customCss')
    <style>
        .seller-order-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, .8fr);
            align-items: start;
            gap: 20px;
        }

        .seller-order-panel {
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
        }

        .seller-order-panel h2 {
            margin: 0 0 18px;
            font-size: 16px;
            font-weight: 700;
        }

        .seller-order-item {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: start;
            padding: 18px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .seller-order-item:first-child {
            padding-top: 0;
        }

        .seller-order-item:last-child {
            border-bottom: 0;
        }

        .seller-order-image,
        .seller-order-image-empty {
            width: 64px;
            height: 64px;
            border-radius: 9px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .seller-order-image-empty {
            display: grid;
            place-items: center;
            color: #94a3b8;
            font-size: 22px;
        }

        .seller-order-item-main {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .seller-order-item-title {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 650;
        }

        .seller-order-item-meta,
        .seller-order-muted {
            color: #64748b;
            font-size: 13px;
        }

        .seller-order-item-total {
            white-space: nowrap;
            font-weight: 700;
        }

        .seller-order-item-actions {
            grid-column: 2 / -1;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 10px;
        }

        .seller-order-reject {
            width: min(100%, 390px);
        }

        .seller-order-reject summary {
            display: inline-block;
            cursor: pointer;
            color: #b42318;
            font-size: 13px;
            font-weight: 600;
        }

        .seller-order-reject form {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .seller-order-reject textarea {
            width: 100%;
            min-height: 68px;
            padding: 9px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            resize: vertical;
        }

        .seller-order-info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .seller-order-info-row:last-child {
            border-bottom: 0;
        }

        @media (max-width: 767px) {
            .seller-order-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .seller-order-panel {
                padding: 16px;
            }

            .seller-order-item {
                grid-template-columns: 52px minmax(0, 1fr);
                gap: 11px;
            }

            .seller-order-image,
            .seller-order-image-empty {
                width: 52px;
                height: 52px;
            }

            .seller-order-item-total {
                grid-column: 2;
            }

            .seller-order-item-actions {
                grid-column: 2;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $pendingCount = $order->items->where('status', 'pending')->count();
        $sellerSubtotal = $order->items->sum('line_total');
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Order {{ $order->order_number }}</h1>
            <p class="page-subtitle">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
        </div>
        <a href="{{ route('seller.orders.index') }}" class="btn-quick-action">
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

    <div class="seller-order-layout">
        <section class="seller-order-panel">
            <h2>Your items in this order <span class="seller-order-muted">({{ $order->items->count() }})</span></h2>

            @foreach ($order->items as $item)
                @php
                    $itemStatusClass = match ($item->status) {
                        'accepted', 'delivered' => 'success',
                        'shipped' => 'pending',
                        'rejected', 'cancelled' => 'failed',
                        default => 'pending',
                    };
                @endphp
                <article class="seller-order-item">
                    @if ($item->image_url)
                        <img src="{{ $item->image_url }}" class="seller-order-image" alt="{{ $item->product_name }}"
                            loading="lazy">
                    @else
                        <div class="seller-order-image-empty" aria-hidden="true"><i class="bi bi-image"></i></div>
                    @endif

                    <div class="seller-order-item-main">
                        <h3 class="seller-order-item-title">{{ $item->product_name }}</h3>
                        <div class="seller-order-item-meta">
                            @if ($item->variant_title)
                                {{ $item->variant_title }} ·
                            @endif
                            Qty {{ $item->quantity }} · {{ \App\Support\Money::format($item->unit_price) }} each
                        </div>
                        @if ($item->sku)
                            <div class="seller-order-item-meta">SKU: {{ $item->sku }}</div>
                        @endif
                        @if ($item->status_reason)
                            <div class="seller-order-item-meta text-danger mt-1">Reason: {{ $item->status_reason }}</div>
                        @endif
                        <span class="badge-table {{ $itemStatusClass }} mt-2">{{ ucfirst($item->status) }}</span>
                    </div>

                    <div class="seller-order-item-total">{{ \App\Support\Money::format($item->line_total) }}</div>

                    @if ($item->canBeDecided())
                        <div class="seller-order-item-actions">
                            <form method="POST" action="{{ route('seller.orders.items.decide', $item) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="decision" value="accepted">
                                <button type="submit" class="btn-quick-action">
                                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                                    <span>Accept item</span>
                                </button>
                            </form>
                            <details class="seller-order-reject">
                                <summary>Reject item</summary>
                                <form method="POST" action="{{ route('seller.orders.items.decide', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="rejected">
                                    <label class="visually-hidden" for="reject-reason-{{ $item->id }}">Reason for
                                        rejecting {{ $item->product_name }}</label>
                                    <textarea id="reject-reason-{{ $item->id }}" name="reason" maxlength="255" required
                                        placeholder="Tell the customer why this item cannot be fulfilled."></textarea>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Confirm rejection</button>
                                </form>
                            </details>
                        </div>
                    @endif
                </article>
            @endforeach

            <div class="seller-order-info-row fw-semibold">
                <span>Your items subtotal</span>
                <span>{{ \App\Support\Money::format($sellerSubtotal) }}</span>
            </div>
            @if ($pendingCount > 0)
                <p class="seller-order-muted mt-3 mb-0">{{ $pendingCount }} item{{ $pendingCount === 1 ? '' : 's' }}
                    need{{ $pendingCount === 1 ? 's' : '' }} your decision.</p>
            @endif
        </section>

        <aside>
            <section class="seller-order-panel mb-3">
                <h2>Customer</h2>
                <div class="seller-order-info-row"><span
                        class="seller-order-muted">Name</span><strong>{{ $order->customer?->name ?? $order->customer_name }}</strong>
                </div>
                <div class="seller-order-info-row"><span class="seller-order-muted">Phone</span><a
                        href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></div>
                @if ($order->customer_email)
                    <div class="seller-order-info-row"><span class="seller-order-muted">Email</span><a
                            href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></div>
                @endif
                <div class="mt-3">
                    <div class="seller-order-muted mb-1">Delivery address</div>
                    <div>{{ $order->delivery_address }}</div>
                </div>

                <div class="mt-3">
                    <iframe width="100%" height="200" style="border:0" loading="lazy"
                        src="https://maps.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}&z=15&output=embed">
                    </iframe>
                </div>

                @if ($order->notes)
                    <div class="mt-3">
                        <div class="seller-order-muted mb-1">Customer notes</div>
                        <div>{{ $order->notes }}</div>
                    </div>
                @endif
            </section>

            <section class="seller-order-panel">
                <h2>Order information</h2>
                <div class="seller-order-info-row"><span class="seller-order-muted">Payment
                        method</span><strong>{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->online_payment_method) }}</strong>
                </div>
                <div class="seller-order-info-row"><span class="seller-order-muted">Payment
                        status</span><strong>{{ ucfirst($order->payment_status) }}</strong></div>
                <div class="seller-order-info-row"><span class="seller-order-muted">Order
                        status</span><strong>{{ ucfirst($order->status) }}</strong></div>
            </section>
        </aside>
    </div>
@endsection
