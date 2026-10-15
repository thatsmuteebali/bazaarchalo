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

        .co-address-map {
            width: 100%;
            height: 280px;
            border-radius: 8px;
            background: var(--bs-light);
        }

        /* ---------- address suggestions dropdown ---------- */
        .co-suggestions {
            position: absolute;
            left: calc(var(--bs-gutter-x, 1.5rem) * .5);
            right: calc(var(--bs-gutter-x, 1.5rem) * .5);
            z-index: 1050;
            max-height: 280px;
            margin: 4px 0 0;
            padding: 6px 0;
            overflow-y: auto;
            list-style: none;
            border: 1px solid rgba(0, 0, 0, .12);
            border-radius: 10px;
            background: var(--bs-white);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
        }

        .co-suggestions[hidden] {
            display: none;
        }

        .co-suggestion {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            cursor: pointer;
        }

        .co-suggestion i {
            margin-top: 3px;
            color: var(--bs-primary);
        }

        .co-suggestion.active,
        .co-suggestion:hover {
            background: rgba(129, 196, 8, .1);
        }

        .co-suggestion-main {
            display: block;
            color: var(--bs-dark);
            font-size: .92rem;
            font-weight: 600;
        }

        .co-suggestion-sub {
            display: block;
            color: var(--bs-gray);
            font-size: .8rem;
        }

        .co-online-options {
            margin: 8px 0 4px 28px;
            padding: 14px 16px;
            border-left: 3px solid var(--bs-primary);
            background: rgba(129, 196, 8, .06);
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
                                        pattern="03[0-9]{9}" maxlength="11"
                                        value="{{ old('phone', $user->phone ?? '') }}"
                                        class="form-control @error('phone') is-invalid @enderror" placeholder="03000000000">
                                    <div class="invalid-feedback">@error('phone') {{ $message }} @else Enter 11 digits with no spaces or hyphens, e.g. 03000000000. @enderror</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="email">Email address <small>(optional; order updates can be sent here)</small></label>
                                    <input type="email" id="email" name="email" autocomplete="email" maxlength="150"
                                        value="{{ old('email', $user->email ?? '') }}"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com">
                                    <div class="invalid-feedback">@error('email') {{ $message }} @else Enter a valid email address. @enderror</div>
                                </div>

                                <div class="col-12 position-relative">
                                    <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                                    {{-- autocomplete="off" so the browser's own autofill does not cover our suggestions --}}
                                    <input type="text" id="address" name="address" autocomplete="off" required minlength="5" maxlength="255"
                                        role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="addressSuggestions"
                                        value="{{ old('address') }}"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Search for your address or enter it manually">
                                    <ul id="addressSuggestions" class="co-suggestions" role="listbox" hidden></ul>
                                    <div class="invalid-feedback">@error('address') {{ $message }} @else Please enter your delivery address. @enderror</div>

                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                        <button type="button" id="useCurrentLocation" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-location-arrow me-1" aria-hidden="true"></i>Use my current location
                                        </button>
                                        <span id="addressStatus" class="small text-muted" role="status" aria-live="polite"></span>
                                    </div>
                                    @if (!empty($googleMapsApiKey))
                                        <div id="checkoutAddressMap" class="co-address-map mt-3" aria-label="Map showing your delivery address"></div>
                                    @else
                                        <p class="small text-muted mt-2 mb-0">Address search and map location will be available after Google Maps is configured.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 2. Payment method --}}
                        <div class="co-card">
                            <div class="co-card-title"><span class="co-step">2</span> Payment method</div>
                            <p class="co-card-sub">Choose how you would like to pay.</p>

                            @php
                                $selectedPayment = old('payment_method', 'cod');
                                $selectedOnlineMethod = old('online_payment_method');
                                $onlineMethods = [
                                    'jazzcash' => 'JazzCash',
                                    'easypaisa' => 'Easypaisa',
                                    'gopayfast' => 'Gopayfast',
                                    'bank_account' => 'Bank Account',
                                ];
                            @endphp

                            <fieldset class="border-0 p-0 m-0">
                                <legend class="visually-hidden">Choose a payment method</legend>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentCod" value="cod"
                                        {{ $selectedPayment === 'cod' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="paymentCod">Cash on Delivery</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentOnline" value="online"
                                        {{ $selectedPayment === 'online' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="paymentOnline">Online Payment</label>
                                </div>
                            </fieldset>

                            <fieldset id="onlinePaymentOptions" class="co-online-options rounded d-none" {{ $selectedPayment === 'online' ? '' : 'hidden' }}>
                                <legend class="fs-6 fw-semibold mb-2">Select online payment method</legend>
                                @foreach ($onlineMethods as $key => $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="online_payment_method" id="onlinePayment{{ ucfirst($key) }}"
                                            value="{{ $key }}" {{ $selectedOnlineMethod === $key ? 'checked' : '' }}>
                                        <label class="form-check-label" for="onlinePayment{{ ucfirst($key) }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                                @error('online_payment_method')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </fieldset>

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
            var address = document.getElementById('address');
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var suggestionsBox = document.getElementById('addressSuggestions');
            var addressStatus = document.getElementById('addressStatus');
            var currentLocationButton = document.getElementById('useCurrentLocation');
            var onlinePaymentOptions = document.getElementById('onlinePaymentOptions');
            var onlinePaymentRadios = onlinePaymentOptions.querySelectorAll('input[name="online_payment_method"]');
            var submitting = false;

            // Keep the mobile number in the exact 11-digit format expected by validation.
            phone.addEventListener('input', function() {
                phone.value = phone.value.replace(/\D/g, '').slice(0, 11);
            });
            phone.value = phone.value.replace(/\D/g, '').slice(0, 11);

            function updateOnlinePaymentOptions() {
                var onlineSelected = document.getElementById('paymentOnline').checked;
                onlinePaymentOptions.hidden = !onlineSelected;
                onlinePaymentRadios.forEach(function(radio) {
                    radio.disabled = !onlineSelected;
                    radio.required = onlineSelected;
                });
            }

            document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
                radio.addEventListener('change', updateOnlinePaymentOptions);
            });
            updateOnlinePaymentOptions();

            /* =====================================================
               Google Maps: address suggestions + map + current location
               Uses the NEW Places API (Places API (New)) and Geocoding API.
               ===================================================== */
            var map = null;
            var marker = null;
            var geocoder = null;
            var AutocompleteSuggestion = null;
            var AutocompleteSessionToken = null;
            var PlaceClass = null;
            var RankPreference = null;
            var sessionToken = null;
            var debounceTimer = null;
            var requestId = 0;
            var currentItems = [];
            var activeIndex = -1;

            function setStatus(text) {
                addressStatus.textContent = text;
            }

            function coord(value, key) {
                return typeof value[key] === 'function' ? value[key]() : value[key];
            }

            function showOnMap(location, viewport) {
                if (!map) return;

                map.setCenter(location);
                if (viewport) {
                    map.fitBounds(viewport);
                } else {
                    map.setZoom(16);
                }

                marker.position = location;
                marker.map = map;

                latInput.value = coord(location, 'lat');
                lngInput.value = coord(location, 'lng');
            }

            function hideSuggestions() {
                suggestionsBox.hidden = true;
                suggestionsBox.innerHTML = '';
                address.setAttribute('aria-expanded', 'false');
                currentItems = [];
                activeIndex = -1;
            }

            function setActive(index) {
                var nodes = suggestionsBox.querySelectorAll('.co-suggestion');
                nodes.forEach(function(node, i) {
                    node.classList.toggle('active', i === index);
                    node.setAttribute('aria-selected', i === index ? 'true' : 'false');
                });
                activeIndex = index;
                if (nodes[index]) {
                    nodes[index].scrollIntoView({ block: 'nearest' });
                }
            }

            function renderSuggestions(items) {
                suggestionsBox.innerHTML = '';
                currentItems = items;
                activeIndex = -1;

                if (!items.length) {
                    hideSuggestions();
                    return;
                }

                items.forEach(function(item, index) {
                    var prediction = item.placePrediction;
                    var li = document.createElement('li');
                    li.className = 'co-suggestion';
                    li.setAttribute('role', 'option');

                    var icon = document.createElement('i');
                    icon.className = 'fas fa-map-marker-alt';
                    icon.setAttribute('aria-hidden', 'true');

                    var text = document.createElement('div');
                    var main = document.createElement('span');
                    main.className = 'co-suggestion-main';
                    main.textContent = prediction.mainText ? prediction.mainText.text : prediction.text.text;
                    text.appendChild(main);

                    if (prediction.secondaryText && prediction.secondaryText.text) {
                        var sub = document.createElement('span');
                        sub.className = 'co-suggestion-sub';
                        sub.textContent = prediction.secondaryText.text;
                        text.appendChild(sub);
                    }

                    li.appendChild(icon);
                    li.appendChild(text);

                    // mousedown (not click) so the input does not lose focus first
                    li.addEventListener('mousedown', function(event) {
                        event.preventDefault();
                        selectSuggestion(index);
                    });
                    li.addEventListener('mouseenter', function() {
                        setActive(index);
                    });

                    suggestionsBox.appendChild(li);
                });

                suggestionsBox.hidden = false;
                address.setAttribute('aria-expanded', 'true');
            }

            async function fetchSuggestions(query) {
                var thisRequest = ++requestId;

                if (!AutocompleteSuggestion) return;

                if (!sessionToken) {
                    sessionToken = new AutocompleteSessionToken();
                }

                try {
                    var response = await AutocompleteSuggestion.fetchAutocompleteSuggestions({
                        input: query,
                        sessionToken: sessionToken,
                        includedRegionCodes: ['pk']
                    });

                    // ignore out-of-date responses
                    if (thisRequest !== requestId) return;

                    renderSuggestions(response.suggestions || []);
                } catch (error) {
                    console.error('Address suggestions failed:', error);
                    hideSuggestions();
                    setStatus('Address suggestions are unavailable right now. You can type your address manually.');
                }
            }

            async function selectSuggestion(index) {
                var item = currentItems[index];
                if (!item) return;

                var place = item.placePrediction.toPlace();
                var fallbackText = item.placePrediction.text.text;
                hideSuggestions();
                address.value = fallbackText;
                setStatus('Loading address...');

                try {
                    await place.fetchFields({ fields: ['formattedAddress', 'location', 'viewport'] });
                    address.value = place.formattedAddress || fallbackText;
                    address.classList.remove('is-invalid');
                    if (place.location) {
                        showOnMap(place.location, place.viewport);
                    }
                    setStatus('Address selected.');
                } catch (error) {
                    console.error('Place details failed:', error);
                    setStatus('Could not load that place on the map, but you can still use the address.');
                }

                // one session = one search + one selection
                sessionToken = null;
            }

            address.addEventListener('input', function() {
                // the typed text no longer matches the picked place
                latInput.value = '';
                lngInput.value = '';

                clearTimeout(debounceTimer);
                var query = address.value.trim();

                if (query.length < 3) {
                    requestId++;
                    hideSuggestions();
                    return;
                }

                debounceTimer = setTimeout(function() {
                    fetchSuggestions(query);
                }, 250);
            });

            address.addEventListener('keydown', function(event) {
                if (suggestionsBox.hidden) return;

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActive((activeIndex + 1) % currentItems.length);
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    setActive((activeIndex - 1 + currentItems.length) % currentItems.length);
                } else if (event.key === 'Enter' && activeIndex >= 0) {
                    event.preventDefault(); // do not submit the checkout form
                    selectSuggestion(activeIndex);
                } else if (event.key === 'Escape') {
                    hideSuggestions();
                }
            });

            address.addEventListener('blur', function() {
                setTimeout(hideSuggestions, 150);
            });

            // Fallback when reverse geocoding is denied or finds nothing:
            // use the nearest place of any kind (Places API New) and show its name + address.
            async function nearestPlaceAddress(location) {
                if (!PlaceClass || !RankPreference) return null;

                var radii = [100, 500, 2000, 5000];

                for (var i = 0; i < radii.length; i++) {
                    try {
                        var result = await PlaceClass.searchNearby({
                            fields: ['displayName', 'formattedAddress'],
                            locationRestriction: { center: location, radius: radii[i] },
                            maxResultCount: 1,
                            rankPreference: RankPreference.DISTANCE
                        });

                        var place = result.places && result.places[0];
                        if (place) {
                            var parts = [];
                            if (place.displayName) parts.push(place.displayName);
                            if (place.formattedAddress) parts.push(place.formattedAddress);
                            return parts.join(', ');
                        }
                    } catch (error) {
                        console.error('Nearby search failed:', error);
                        return null;
                    }
                }

                return null;
            }

            // Reverse geocoding often returns a Plus Code (e.g. "7MQ2+XX") or an unnamed road as the
            // first result. Prefer a real street / area address instead.
            function pickBestAddress(results) {
                var wanted = [
                    ['street_address', 'premise', 'subpremise'],
                    ['route'],
                    ['neighborhood', 'sublocality', 'sublocality_level_1'],
                    ['locality']
                ];

                for (var i = 0; i < wanted.length; i++) {
                    for (var j = 0; j < results.length; j++) {
                        var types = results[j].types || [];
                        var matches = wanted[i].some(function(type) { return types.indexOf(type) !== -1; });
                        if (matches && !/^[A-Z0-9]{4}\+[A-Z0-9]{2,}/.test(results[j].formatted_address)) {
                            return results[j];
                        }
                    }
                }

                // last resort: first result that is not a plus code
                for (var k = 0; k < results.length; k++) {
                    if ((results[k].types || []).indexOf('plus_code') === -1) {
                        return results[k];
                    }
                }

                return results[0] || null;
            }

            currentLocationButton.addEventListener('click', function() {
                if (!map || !geocoder) {
                    setStatus('Google Maps is not ready. Check that GOOGLE_MAPS_API_KEY is set in your .env file.');
                    return;
                }

                if (!navigator.geolocation) {
                    setStatus('Location is not supported by this browser.');
                    return;
                }

                setStatus('Finding your location...');
                currentLocationButton.disabled = true;

                navigator.geolocation.getCurrentPosition(async function(position) {
                    var location = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    showOnMap(location, null);

                    var found = null;

                    try {
                        var response = await geocoder.geocode({ location: location });
                        var best = pickBestAddress(response.results || []);
                        found = best ? best.formatted_address : null;
                    } catch (error) {
                        console.warn('Geocoding failed, trying nearby places instead:', error);
                    }

                    if (!found) {
                        found = await nearestPlaceAddress(location);
                    }

                    if (found) {
                        address.value = found;
                        address.classList.remove('is-invalid');
                        hideSuggestions();
                        setStatus('Your current location was added.');
                    } else {
                        setStatus('Location shown on the map, but no address was found nearby. Please type it manually.');
                    }

                    currentLocationButton.disabled = false;
                }, function(error) {
                    currentLocationButton.disabled = false;
                    setStatus(error.code === 1
                        ? 'Location permission was denied. Please allow it or enter your address manually.'
                        : 'Could not get your location. Please enter your address manually.');
                }, { enableHighAccuracy: true, timeout: 10000 });
            });

            @if (!empty($googleMapsApiKey))
                // Called by Google if the key is rejected (billing off, wrong restrictions, API not enabled...)
                window.gm_authFailure = function() {
                    setStatus('Google Maps rejected the API key. Check billing, enabled APIs and key restrictions.');
                };

                window.initCheckoutAddressMap = async function() {
                    try {
                        var mapsLib = await google.maps.importLibrary('maps');
                        var markerLib = await google.maps.importLibrary('marker');
                        var placesLib = await google.maps.importLibrary('places');
                        var geocodingLib = await google.maps.importLibrary('geocoding');

                        AutocompleteSuggestion = placesLib.AutocompleteSuggestion;
                        AutocompleteSessionToken = placesLib.AutocompleteSessionToken;
                        PlaceClass = placesLib.Place;
                        RankPreference = placesLib.SearchNearbyRankPreference;
                        geocoder = new geocodingLib.Geocoder();

                        map = new mapsLib.Map(document.getElementById('checkoutAddressMap'), {
                            center: { lat: 30.3753, lng: 69.3451 },
                            zoom: 5,
                            mapId: 'DEMO_MAP_ID', // required by AdvancedMarkerElement
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: false
                        });
                        marker = new markerLib.AdvancedMarkerElement({ map: null });

                        // restore the pin after a validation error (old input)
                        if (latInput.value && lngInput.value) {
                            showOnMap({ lat: parseFloat(latInput.value), lng: parseFloat(lngInput.value) }, null);
                        }
                    } catch (error) {
                        console.error('Google Maps init failed:', error);
                        setStatus('Google Maps could not start. You can still type your address manually.');
                    }
                };

                var googleMapsScript = document.createElement('script');
                googleMapsScript.src = 'https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleMapsApiKey) }}&v=weekly&loading=async&callback=initCheckoutAddressMap';
                googleMapsScript.async = true;
                googleMapsScript.defer = true;
                googleMapsScript.onerror = function() {
                    setStatus('Google Maps could not be loaded. Check your internet connection and API key.');
                };
                document.head.appendChild(googleMapsScript);
            @endif

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
