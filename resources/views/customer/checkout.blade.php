@extends('layouts.app')

@section('customCss')
    <style>
        .co-card {
            margin-bottom: 24px;
            padding: 28px;
            border: 1px solid rgba(129, 196, 8, .18);
            border-radius: 14px;
            background: var(--bs-white);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .04);
        }

        .co-card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
            font-family: Raleway, sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .co-card-sub {
            margin-bottom: 22px;
            color: var(--bs-gray);
            font-size: .9rem;
        }

        .co-step {
            display: inline-flex;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--bs-secondary);
            color: var(--bs-white);
            font-size: .9rem;
            font-weight: 800;
        }

        .co-card .form-label {
            margin-bottom: 6px;
            color: var(--bs-dark);
            font-size: .9rem;
            font-weight: 600;
        }

        .co-card .form-label small {
            color: var(--bs-gray);
            font-weight: 400;
        }

        .co-card .form-control,
        .co-card .form-select {
            padding: 12px 14px;
            border-color: rgba(0, 0, 0, .15);
            border-radius: 8px;
        }

        .co-card .form-control:focus,
        .co-card .form-select:focus {
            border-color: var(--bs-secondary);
            box-shadow: 0 0 0 .2rem rgba(255, 181, 36, .18);
        }

        /* ---------- payment options ---------- */
        .co-pay {
            position: relative;
            display: block;
            margin: 0 0 12px;
            cursor: pointer;
        }

        .co-pay-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .co-pay-body {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border: 1.5px solid rgba(0, 0, 0, .12);
            border-radius: 12px;
            background: var(--bs-white);
            transition: .2s ease;
        }

        .co-pay:hover .co-pay-body {
            border-color: var(--bs-primary);
        }

        .co-pay-input:checked+.co-pay-body {
            border-color: var(--bs-primary);
            background: rgba(129, 196, 8, .07);
            box-shadow: 0 0 0 3px rgba(129, 196, 8, .15);
        }

        .co-pay-input:focus-visible+.co-pay-body {
            outline: 2px solid var(--bs-secondary);
            outline-offset: 2px;
        }

        .co-pay-radio {
            position: relative;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 2px;
            border: 2px solid rgba(0, 0, 0, .25);
            border-radius: 50%;
        }

        .co-pay-input:checked+.co-pay-body .co-pay-radio {
            border-color: var(--bs-primary);
        }

        .co-pay-input:checked+.co-pay-body .co-pay-radio::after {
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: var(--bs-primary);
            content: "";
        }

        .co-pay-icon {
            display: flex;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 181, 36, .16);
            color: var(--bs-secondary);
            font-size: 1.2rem;
        }

        .co-pay-title {
            display: block;
            color: var(--bs-dark);
            font-weight: 700;
        }

        .co-pay-desc {
            display: block;
            margin-top: 2px;
            color: var(--bs-gray);
            font-size: .85rem;
            line-height: 1.5;
        }

        /* ---------- order summary ---------- */
        .co-summary {
            position: sticky;
            top: 130px;
        }

        .co-summary-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .co-summary-head h2 {
            margin: 0;
            font-family: Raleway, sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .co-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(0, 0, 0, .07);
        }

        .co-item:last-child {
            border-bottom: 0;
        }

        .co-item-img {
            position: relative;
            flex: 0 0 64px;
        }

        .co-item-img img {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
        }

        .co-item-qty {
            position: absolute;
            top: -8px;
            right: -8px;
            display: flex;
            min-width: 22px;
            height: 22px;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--bs-primary);
            color: var(--bs-white);
            font-size: .72rem;
            font-weight: 700;
        }

        .co-item-info {
            flex: 1;
            min-width: 0;
        }

        .co-item-name {
            display: block;
            color: var(--bs-dark);
            font-size: .92rem;
            font-weight: 700;
        }

        .co-item-name:hover {
            color: var(--bs-primary);
        }

        .co-item-total {
            font-size: .92rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .co-free-ship {
            margin-top: 6px;
            padding: 12px 14px;
            border-radius: 10px;
            background: var(--bs-light);
        }

        .co-totals {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(0, 0, 0, .1);
        }

        .co-total-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            color: var(--bs-gray);
        }

        .co-grand {
            margin: 14px 0 0;
            padding-top: 14px;
            border-top: 1px dashed rgba(0, 0, 0, .15);
            color: var(--bs-dark);
            font-size: 1.15rem;
            font-weight: 800;
        }

        .co-grand span:last-child {
            color: var(--bs-primary);
        }

        .co-secure {
            margin-top: 14px;
            color: var(--bs-gray);
            font-size: .8rem;
            text-align: center;
        }

        @media (max-width: 991px) {
            .co-summary {
                position: static;
            }
        }

        @media (max-width: 575px) {
            .co-card {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $wired = \Illuminate\Support\Facades\Route::has('customer.checkout.place');
        $placeUrl = $wired ? route('customer.checkout.place') : '#';
    @endphp

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.cart') }}">Cart</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-4">
            <form method="POST" action="{{ $placeUrl }}" id="checkoutForm" novalidate data-wired="{{ $wired ? '1' : '0' }}">
                @csrf

                <div class="row g-5">
                    {{-- LEFT: details --}}
                    <div class="col-lg-7">

                        {{-- 1. Delivery information --}}
                        <div class="co-card">
                            <div class="co-card-title"><span class="co-step">1</span> Delivery information</div>
                            <p class="co-card-sub">We deliver all over Pakistan. Please make sure your mobile number is correct so our rider can reach you.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Full name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" autocomplete="name" required maxlength="100"
                                        value="{{ old('name', $user->name ?? '') }}"
                                        class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Ahmed Khan">
                                    <div class="invalid-feedback">@error('name') {{ $message }} @else Please enter your full name. @enderror</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="phone">Mobile number <span class="text-danger">*</span></label>
                                    <input type="tel" id="phone" name="phone" autocomplete="tel" inputmode="numeric" required
                                        pattern="03[0-9]{2}-?[0-9]{7}" maxlength="12"
                                        value="{{ old('phone', $user->phone ?? '') }}"
                                        class="form-control @error('phone') is-invalid @enderror" placeholder="0300-1234567">
                                    <div class="invalid-feedback">@error('phone') {{ $message }} @else Enter a valid mobile number, for example 0300-1234567. @enderror</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="email">Email address <span class="text-danger">*</span> <small>(order updates are sent here)</small></label>
                                    <input type="email" id="email" name="email" autocomplete="email" required maxlength="150"
                                        value="{{ old('email', $user->email ?? '') }}"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com">
                                    <div class="invalid-feedback">@error('email') {{ $message }} @else Please enter a valid email address. @enderror</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="province">Province <span class="text-danger">*</span></label>
                                    @php $selectedProvince = old('province'); @endphp
                                    <select id="province" name="province" autocomplete="address-level1" required
                                        class="form-select @error('province') is-invalid @enderror">
                                        <option value="" disabled {{ $selectedProvince ? '' : 'selected' }}>Select province</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province }}" {{ $selectedProvince === $province ? 'selected' : '' }}>{{ $province }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">@error('province') {{ $message }} @else Please select your province. @enderror</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" id="city" name="city" list="pkCities" autocomplete="address-level2" required maxlength="80"
                                        value="{{ old('city') }}"
                                        class="form-control @error('city') is-invalid @enderror" placeholder="Start typing your city">
                                    {{-- <datalist id="pkCities">
                                        @foreach ($cities as $city)
                                            <option value="{{ $city }}"></option>
                                        @endforeach
                                    </datalist> --}}
                                    <div class="invalid-feedback">@error('city') {{ $message }} @else Please enter your city. @enderror</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="address">Complete address <span class="text-danger">*</span></label>
                                    <input type="text" id="address" name="address" autocomplete="address-line1" required minlength="10" maxlength="255"
                                        value="{{ old('address') }}"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="House / flat no., street, area or society">
                                    <div class="invalid-feedback">@error('address') {{ $message }} @else Please enter your full delivery address (at least 10 characters). @enderror</div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label" for="landmark">Nearby landmark <small>(optional)</small></label>
                                    <input type="text" id="landmark" name="landmark" maxlength="150"
                                        value="{{ old('landmark') }}"
                                        class="form-control @error('landmark') is-invalid @enderror" placeholder="e.g. Near Jinnah Park">
                                    <div class="invalid-feedback">@error('landmark') {{ $message }} @enderror</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="postal_code">Postal code <small>(optional)</small></label>
                                    <input type="text" id="postal_code" name="postal_code" autocomplete="postal-code" inputmode="numeric"
                                        pattern="[0-9]{5}" maxlength="5" value="{{ old('postal_code') }}"
                                        class="form-control @error('postal_code') is-invalid @enderror" placeholder="54000">
                                    <div class="invalid-feedback">@error('postal_code') {{ $message }} @else Postal code must be 5 digits. @enderror</div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Payment method --}}
                        <div class="co-card">
                            <div class="co-card-title"><span class="co-step">2</span> Payment method</div>
                            <p class="co-card-sub">Choose how you would like to pay.</p>

                            @php $selectedPayment = old('payment_method', array_key_first($payments)); @endphp

                            @foreach ($payments as $key => $payment)
                                <label class="co-pay">
                                    <input type="radio" class="co-pay-input" name="payment_method" value="{{ $key }}"
                                        {{ $selectedPayment === $key ? 'checked' : '' }} required>
                                    <span class="co-pay-body">
                                        <span class="co-pay-radio" aria-hidden="true"></span>
                                        <span class="co-pay-icon" aria-hidden="true"><i class="fas {{ $payment['icon'] }}"></i></span>
                                        <span>
                                            <span class="co-pay-title">{{ $payment['label'] }}</span>
                                            <span class="co-pay-desc">{{ $payment['description'] }}</span>
                                        </span>
                                    </span>
                                </label>
                            @endforeach

                            @error('payment_method')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 3. Notes --}}
                        <div class="co-card">
                            <div class="co-card-title"><span class="co-step">3</span> Order notes <small class="text-muted fw-normal fs-6">(optional)</small></div>
                            <p class="co-card-sub">Anything the seller or rider should know, such as delivery timing.</p>
                            <textarea name="notes" id="notes" rows="4" maxlength="500" spellcheck="false"
                                class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Notes about your order, e.g. call before delivery">{{ old('notes') }}</textarea>
                            <div class="invalid-feedback">@error('notes') {{ $message }} @enderror</div>
                        </div>
                    </div>

                    {{-- RIGHT: order summary --}}
                    <div class="col-lg-5">
                        <div class="co-summary">
                            <div class="co-card mb-0">
                                <div class="co-summary-head">
                                    <h2>Order summary</h2>
                                    <a href="{{ route('frontend.cart') }}" class="small fw-semibold">Edit cart</a>
                                </div>

                                {{-- replaced by fresh HTML from the server when the cart changes --}}
                                <div id="checkoutSummary">
                                    @include('customer.partials.checkout-summary', ['cart' => $cart, 'totals' => $totals])
                                </div>

                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms"
                                        name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="terms">
                                        I have read and agree to the
                                        <a href="{{ route('frontend.terms-and-conditions') }}" target="_blank" rel="noopener">Terms &amp; Conditions</a>
                                        and
                                        <a href="{{ route('frontend.refund-policy') }}" target="_blank" rel="noopener">Refund Policy</a>.
                                    </label>
                                    <div class="invalid-feedback">@error('terms') {{ $message }} @else Please accept the terms to continue. @enderror</div>
                                </div>

                                <button type="submit" id="placeOrderBtn" class="btn btn-primary rounded-pill w-100 py-3 mt-4 text-uppercase">
                                    <i class="fas fa-lock me-2" aria-hidden="true"></i><span>Place Order</span>
                                </button>

                                <p class="co-secure mb-0">
                                    <i class="fas fa-shield-alt me-1" aria-hidden="true"></i>
                                    Your details are used only to process and deliver your order.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->
@endsection

@section('customjs')
    <script>
        (function() {
            var form = document.getElementById('checkoutForm');
            var button = document.getElementById('placeOrderBtn');
            var buttonLabel = button.querySelector('span');
            var phone = document.getElementById('phone');
            var submitting = false;

            // Mobile number: digits only, shown as 0300-1234567 (also understands +92 300 1234567)
            phone.addEventListener('input', function() {
                var digits = phone.value.replace(/\D/g, '');

                if (digits.indexOf('92') === 0 && digits.length > 10) {
                    digits = '0' + digits.slice(2);
                }

                digits = digits.slice(0, 11);
                phone.value = digits.length > 4 ? digits.slice(0, 4) + '-' + digits.slice(4) : digits;
            });

            // Postal code: digits only
            document.getElementById('postal_code').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 5);
            });

            // The cart changed while the customer is on this page (for example in the cart drawer)
            $(document).on('cart:page-updated', function() {
                var inner = document.getElementById('checkoutSummaryInner');
                var empty = !inner || inner.dataset.empty === '1';

                button.disabled = empty || submitting;
            });

            form.addEventListener('submit', function(event) {
                var inner = document.getElementById('checkoutSummaryInner');

                if (!inner || inner.dataset.empty === '1') {
                    event.preventDefault();
                    window.BazaarCart.toast('Your cart is empty.', 'error');
                    return;
                }

                // show every problem at once and jump to the first one
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.classList.add('was-validated');

                    var firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalid.focus({
                            preventScroll: true
                        });
                    }
                    return;
                }

                // "Place Order" is not connected yet (no customer.checkout.place route)
                if (form.dataset.wired !== '1') {
                    event.preventDefault();
                    window.BazaarCart.toast('Your details look good. Order placing is not connected yet.', 'info');
                    return;
                }

                if (submitting) {
                    event.preventDefault();
                    return;
                }

                submitting = true;
                button.disabled = true;
                buttonLabel.textContent = 'Placing order...';
            });
        })();
    </script>
@endsection
