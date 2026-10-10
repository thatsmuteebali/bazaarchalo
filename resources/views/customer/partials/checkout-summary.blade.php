{{-- Items + totals of the checkout page. Also returned as HTML when the cart changes while the customer is on this page.
     Needs $cart (CartService::summary()) and $totals (CheckoutService::totals()). --}}
@php
    $items = $cart['items'] ?? [];
@endphp

<div id="checkoutSummaryInner" data-empty="{{ empty($items) ? '1' : '0' }}">
    @if (empty($items))
        <div class="text-center py-4">
            <i class="fas fa-shopping-bag fa-2x text-secondary mb-3" aria-hidden="true"></i>
            <p class="text-muted mb-3">Your cart is empty.</p>
            <a href="{{ route('frontend.shop') }}" class="btn btn-outline-primary rounded-pill px-4">Continue shopping</a>
        </div>
    @else
        <div class="co-items">
            @foreach ($items as $item)
                <div class="co-item">
                    <a href="{{ $item['url'] }}" class="co-item-img">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                        <span class="co-item-qty" title="Quantity">{{ $item['qty'] }}</span>
                    </a>
                    <div class="co-item-info">
                        <a href="{{ $item['url'] }}" class="co-item-name">{{ $item['name'] }}</a>
                        @if ($item['variant_title'])
                            <small class="d-block text-muted">{{ $item['variant_title'] }}</small>
                        @endif
                        <small class="d-block text-muted">{{ $item['qty'] }} &times; @money($item['price'])</small>
                    </div>
                    <div class="co-item-total">@money($item['line_total'])</div>
                </div>
            @endforeach
        </div>

        @if ($totals['remaining_for_free'] !== null)
            <div class="co-free-ship">
                <div class="small mb-2">
                    <i class="fas fa-truck text-secondary me-1" aria-hidden="true"></i>
                    Add <strong>@money($totals['remaining_for_free'])</strong> more for free delivery
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totals['progress'] }}%"
                        aria-valuenow="{{ $totals['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        @elseif ($totals['free_over'] !== null && $totals['shipping'] == 0)
            <div class="co-free-ship small">
                <i class="fas fa-check-circle text-primary me-1" aria-hidden="true"></i>
                You have free delivery on this order.
            </div>
        @endif

        <div class="co-totals">
            <div class="co-total-row">
                <span>Subtotal ({{ $cart['count'] }} {{ $cart['count'] === 1 ? 'item' : 'items' }})</span>
                <span>@money($totals['subtotal'])</span>
            </div>
            <div class="co-total-row">
                <span>Delivery</span>
                @if ($totals['shipping'] > 0)
                    <span>@money($totals['shipping'])</span>
                @else
                    <span class="text-primary fw-semibold">Free</span>
                @endif
            </div>
            <div class="co-total-row co-grand">
                <span>Total</span>
                <span>@money($totals['total'])</span>
            </div>
        </div>
    @endif
</div>
