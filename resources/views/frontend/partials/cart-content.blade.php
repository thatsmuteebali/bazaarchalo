{{-- Cart table + totals. Used by the cart page and returned as HTML after every cart change. Needs $cart (CartService::summary()). --}}
@php
    $items = $cart['items'] ?? [];
@endphp

@if (empty($items))
    <div class="text-center py-5">
        <i class="fas fa-shopping-bag fa-3x text-secondary mb-4" aria-hidden="true"></i>
        <h3 class="mb-3">Your cart is empty</h3>
        <p class="text-muted mb-4">Looks like you have not added anything yet.</p>
        <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill px-5 py-3">Start shopping</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Total</th>
                    <th scope="col" class="text-end">Remove</th>
                </tr>
            </thead>
            <tbody id="cartPageItems">
                @foreach ($items as $item)
                    <tr data-id="{{ $item['key'] }}">
                        <td>
                            <a href="{{ $item['url'] }}">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                    class="cart-item-img img-fluid rounded-circle">
                            </a>
                        </td>
                        <td>
                            <a href="{{ $item['url'] }}" class="cart-item-name fw-semibold text-dark">{{ $item['name'] }}</a>
                            @if ($item['variant_title'])
                                <small class="d-block text-muted">{{ $item['variant_title'] }}</small>
                            @endif
                        </td>
                        <td>@money($item['price'])</td>
                        <td>
                            <div class="cart-qty">
                                <button type="button" class="btn btn-sm rounded-circle bg-light border"
                                    data-action="decrease" aria-label="Decrease quantity">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <span class="cart-qty-value">{{ $item['qty'] }}</span>
                                <button type="button" class="btn btn-sm rounded-circle bg-light border"
                                    data-action="increase" aria-label="Increase quantity"
                                    @disabled($item['qty'] >= $item['stock'])
                                    title="{{ $item['qty'] >= $item['stock'] ? 'Only ' . $item['stock'] . ' available' : '' }}">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            @if ($item['qty'] >= $item['stock'])
                                <small class="d-block text-muted mt-1">Maximum available</small>
                            @endif
                        </td>
                        <td class="fw-semibold">@money($item['line_total'])</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-md rounded-circle bg-light border"
                                data-action="remove" aria-label="Remove {{ $item['name'] }}">
                                <i class="fa fa-times text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row g-4 justify-content-between mt-2">
        <div class="col-lg-6 align-self-start">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a href="{{ route('frontend.shop') }}" class="btn btn-outline-primary rounded-pill px-4 py-3">
                    <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Continue shopping
                </a>
                <button type="button" class="btn btn-link text-danger" data-cart-clear>
                    <i class="fas fa-trash-alt me-1" aria-hidden="true"></i>Clear cart
                </button>
            </div>
        </div>

        <div class="col-sm-8 col-md-7 col-lg-5 col-xl-4">
            <div class="bg-light rounded">
                <div class="p-4">
                    <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0 me-4">Items:</h5>
                        <p class="mb-0">{{ $cart['count'] }}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0 me-4">Subtotal:</h5>
                        <p class="mb-0" id="cartPageSubtotal">@money($cart['subtotal'])</p>
                    </div>
                    <p class="text-muted small mt-3 mb-0">Shipping is calculated at checkout.</p>
                </div>
                <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                    <h5 class="mb-0 ps-4 me-4">Total</h5>
                    <p class="mb-0 pe-4 fw-bold" id="cartPageTotal">@money($cart['subtotal'])</p>
                </div>

                <a href="{{ route('customer.checkout') }}" id="cartPageCheckout"
                    class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4">Proceed
                    Checkout</a>
            </div>
        </div>
    </div>
@endif
