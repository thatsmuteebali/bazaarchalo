@extends('layouts.admin')

@section('customCss')
    <style>
        .admin-order-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, .8fr);
            align-items: start;
            gap: 20px;
        }

        .admin-order-panel {
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
        }

        .admin-order-panel h2 {
            margin: 0 0 18px;
            font-size: 16px;
            font-weight: 700;
        }

        .admin-order-item {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: start;
            padding: 18px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .admin-order-item:first-child {
            padding-top: 0;
        }

        .admin-order-item:last-child {
            border-bottom: 0;
        }

        .admin-order-image,
        .admin-order-image-empty {
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

        .admin-order-item-main {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .admin-order-item-title {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 650;
        }

        .admin-order-muted {
            color: #64748b;
            font-size: 13px;
        }

        .admin-order-item-total {
            white-space: nowrap;
            font-weight: 700;
        }

        .admin-order-item-actions {
            grid-column: 2 / -1;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 10px;
        }

        .admin-order-reject {
            width: min(100%, 390px);
        }

        .admin-order-reject summary {
            display: inline-block;
            cursor: pointer;
            color: #b42318;
            font-size: 13px;
            font-weight: 600;
        }

        .admin-order-reject form {
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .admin-order-reject textarea {
            width: 100%;
            min-height: 68px;
            padding: 9px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            resize: vertical;
        }

        .admin-order-info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .admin-order-info-row:last-child {
            border-bottom: 0;
        }

        .admin-tracking-event {
            padding: 15px 0;
            border-bottom: 1px solid #edf1f5;
        }

        .admin-tracking-event:last-child {
            border-bottom: 0;
        }

        .admin-tracking-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .admin-tracking-form .form-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 767px) {
            .admin-order-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .admin-order-panel {
                padding: 16px;
            }

            .admin-order-item {
                grid-template-columns: 52px minmax(0, 1fr);
                gap: 11px;
            }

            .admin-order-image,
            .admin-order-image-empty {
                width: 52px;
                height: 52px;
            }

            .admin-order-item-total {
                grid-column: 2;
            }

            .admin-order-item-actions {
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
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Order {{ $order->order_number }}</h1>
            <p class="page-subtitle">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn-quick-action">
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

    <div class="admin-order-layout">
        <div>
            <section class="admin-order-panel mb-3">
                <h2>Order items <span class="admin-order-muted">({{ $order->items->count() }})</span></h2>

                @foreach ($order->items as $item)
                    @php
                        $itemStatusClass = match ($item->status) {
                            'accepted', 'shipped', 'delivered' => 'success',
                            'rejected', 'cancelled' => 'failed',
                            default => 'pending',
                        };
                    @endphp
                    <article class="admin-order-item">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" class="admin-order-image" alt="{{ $item->product_name }}"
                                loading="lazy">
                        @else
                            <div class="admin-order-image-empty" aria-hidden="true"><i class="bi bi-image"></i></div>
                        @endif

                        <div class="admin-order-item-main">
                            <h3 class="admin-order-item-title">{{ $item->product_name }}</h3>
                            <div class="admin-order-muted">
                                @if ($item->variant_title)
                                    {{ $item->variant_title }} ·
                                @endif
                                Qty {{ $item->quantity }} · {{ \App\Support\Money::format($item->unit_price) }} each
                            </div>
                            <div class="admin-order-muted">
                                Seller: {{ $item->seller?->name ?? 'Unknown seller' }}
                                @if ($item->shop_name)
                                    · Shop: {{ $item->shop_name }}
                                @endif
                            </div>
                            @if ($item->sku)
                                <div class="admin-order-muted">SKU: {{ $item->sku }}</div>
                            @endif
                            @if ($item->status_reason)
                                <div class="admin-order-muted text-danger mt-1">Reason: {{ $item->status_reason }}</div>
                            @endif
                            <span class="badge-table {{ $itemStatusClass }} mt-2">{{ ucfirst($item->status) }}</span>
                        </div>

                        <div class="admin-order-item-total">{{ \App\Support\Money::format($item->line_total) }}</div>

                        @if ($item->canBeDecided())
                            <div class="admin-order-item-actions">
                                <form method="POST"
                                    action="{{ route('admin.orders.items.decide', [$order, $item]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="accepted">
                                    <button type="submit" class="btn-quick-action">
                                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                                        <span>Accept item</span>
                                    </button>
                                </form>
                                <details class="admin-order-reject">
                                    <summary>Reject item</summary>
                                    <form method="POST"
                                        action="{{ route('admin.orders.items.decide', [$order, $item]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="rejected">
                                        <label class="visually-hidden" for="admin-reject-reason-{{ $item->id }}">
                                            Reason for rejecting {{ $item->product_name }}
                                        </label>
                                        <textarea id="admin-reject-reason-{{ $item->id }}" name="reason" maxlength="255"
                                            required placeholder="Tell the customer why this item cannot be fulfilled."></textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Confirm rejection</button>
                                    </form>
                                </details>
                            </div>
                        @endif
                    </article>
                @endforeach

                <div class="admin-order-info-row fw-semibold">
                    <span>Items subtotal</span>
                    <span>{{ \App\Support\Money::format($order->subtotal) }}</span>
                </div>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Shipping</span>
                    <span>{{ \App\Support\Money::format($order->shipping_total) }}</span>
                </div>
                <div class="admin-order-info-row fw-semibold">
                    <span>Payable now</span>
                    <span>{{ \App\Support\Money::format($order->payable_total) }}</span>
                </div>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Original total</span>
                    <span>{{ \App\Support\Money::format($order->total) }}</span>
                </div>
            </section>

            <section class="admin-order-panel">
                <h2>Order tracking</h2>
                @forelse ($order->tracking as $event)
                    <article class="admin-tracking-event">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <strong>{{ $event->title }}</strong>
                                @if ($event->item)
                                    <div class="admin-order-muted">{{ $event->item->product_name }}</div>
                                @endif
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge-table {{ $event->is_public ? 'success' : 'pending' }}">
                                    {{ $event->is_public ? 'Customer visible' : 'Internal' }}
                                </span>
                                <span class="badge-table pending">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</span>
                            </div>
                        </div>
                        @if ($event->description)
                            <p class="mb-1 mt-2">{{ $event->description }}</p>
                        @endif
                        @if ($event->courier_name || $event->tracking_number)
                            <div class="admin-order-muted">
                                {{ $event->courier_name }}
                                @if ($event->tracking_number)
                                    · Tracking number: {{ $event->tracking_number }}
                                @endif
                            </div>
                        @endif
                        @if ($event->location)
                            <div class="admin-order-muted">{{ $event->location }}</div>
                        @endif
                        <small class="admin-order-muted">
                            {{ $event->created_at->format('M j, Y g:i A') }} · {{ $event->creator?->name ?? 'System' }}
                        </small>
                    </article>
                @empty
                    <p class="admin-order-muted mb-0">No tracking updates have been recorded.</p>
                @endforelse

                <hr class="my-4">
                <h3 class="h6 mb-3">Add tracking update</h3>
                <p class="admin-order-muted">
                    Updates are recorded in the timeline and cannot be edited or deleted. Mark an item as accepted
                    before shipping it; delivered items must already be shipped.
                </p>
                <form method="POST" action="{{ route('admin.orders.tracking.store', $order) }}" class="admin-tracking-form">
                    @csrf
                    <div>
                        <label for="tracking-status" class="form-label">Event type</label>
                        <select id="tracking-status" name="status" class="form-select" required>
                            <option value="note" @selected(old('status', 'note') === 'note')>Note</option>
                            <option value="shipped" @selected(old('status') === 'shipped')>Shipped</option>
                            <option value="delivered" @selected(old('status') === 'delivered')>Delivered</option>
                        </select>
                    </div>
                    <div>
                        <label for="tracking-item" class="form-label">Related item</label>
                        <select id="tracking-item" name="order_item_id" class="form-select">
                            <option value="">Whole order</option>
                            @foreach ($order->items as $item)
                                <option value="{{ $item->id }}" @selected((string) old('order_item_id') === (string) $item->id)>
                                    {{ $item->product_name }} · {{ $item->shop_name ?? 'No shop' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-full">
                        <label for="tracking-title" class="form-label">Title</label>
                        <input id="tracking-title" name="title" type="text" class="form-control" maxlength="150"
                            value="{{ old('title') }}" required>
                    </div>
                    <div class="form-full">
                        <label for="tracking-description" class="form-label">Description</label>
                        <textarea id="tracking-description" name="description" class="form-control" rows="3"
                            maxlength="2000">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="tracking-courier" class="form-label">Courier</label>
                        <input id="tracking-courier" name="courier_name" type="text" class="form-control" maxlength="80"
                            value="{{ old('courier_name') }}">
                    </div>
                    <div>
                        <label for="tracking-number" class="form-label">Tracking number</label>
                        <input id="tracking-number" name="tracking_number" type="text" class="form-control" maxlength="100"
                            value="{{ old('tracking_number') }}">
                    </div>
                    <div>
                        <label for="tracking-location" class="form-label">Location</label>
                        <input id="tracking-location" name="location" type="text" class="form-control" maxlength="150"
                            value="{{ old('location') }}">
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input id="tracking-public" name="is_public" type="checkbox" class="form-check-input" value="1"
                                @checked(old('is_public', '1') === '1')>
                            <label for="tracking-public" class="form-check-label">Show this update to the customer</label>
                        </div>
                    </div>
                    <div class="form-full">
                        <button type="submit" class="btn-quick-action">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                            <span>Add tracking update</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <aside>
            <section class="admin-order-panel mb-3">
                <h2>Customer</h2>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Name</span>
                    <strong>{{ $order->customer?->name ?? $order->customer_name }}</strong>
                </div>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Phone</span>
                    <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                </div>
                @if ($order->customer_email)
                    <div class="admin-order-info-row">
                        <span class="admin-order-muted">Email</span>
                        <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>
                    </div>
                @endif
                <div class="mt-3">
                    <div class="admin-order-muted mb-1">Delivery address</div>
                    <div>{{ $order->delivery_address }}</div>
                </div>
                @if ($order->notes)
                    <div class="mt-3">
                        <div class="admin-order-muted mb-1">Customer notes</div>
                        <div>{{ $order->notes }}</div>
                    </div>
                @endif
            </section>

            <section class="admin-order-panel">
                <h2>Order information</h2>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Order status</span>
                    <span class="badge-table {{ $orderStatusClass }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Payment method</span>
                    <strong>{{ $order->payment_method_label }}</strong>
                </div>
                @if ($order->online_payment_method)
                    <div class="admin-order-info-row">
                        <span class="admin-order-muted">Online method</span>
                        <strong>{{ ucfirst($order->online_payment_method) }}</strong>
                    </div>
                @endif
                <div class="admin-order-info-row">
                    <span class="admin-order-muted">Payment status</span>
                    <span class="badge-table {{ $paymentStatusClass }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if ($order->paid_at)
                    <div class="admin-order-info-row">
                        <span class="admin-order-muted">Paid at</span>
                        <strong>{{ $order->paid_at->format('M j, Y g:i A') }}</strong>
                    </div>
                @endif
            </section>
        </aside>
    </div>
@endsection
