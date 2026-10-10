@extends('layouts.app')

@section('customCss')
    <style>
        /* ---------- Gallery ---------- */
        .pd-gallery-main {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            border: 1px solid rgba(129, 196, 8, .15);
            border-radius: 14px;
            background: var(--bs-light);
        }

        .pd-gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s ease, opacity .25s ease;
        }

        .pd-gallery-main:hover img {
            transform: scale(1.06);
        }

        .pd-gallery-main img.is-changing {
            opacity: 0;
        }

        .pd-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 2;
            padding: 5px 14px;
            border-radius: 999px;
            color: var(--bs-white);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .pd-badge-sale {
            background: #e5484d;
        }

        .pd-badge-out {
            background: var(--bs-dark);
        }

        .pd-thumbs {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            padding-bottom: 4px;
            overflow-x: auto;
        }

        .pd-thumb {
            flex: 0 0 78px;
            width: 78px;
            height: 78px;
            padding: 0;
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: 10px;
            background: var(--bs-light);
            opacity: .75;
            transition: .25s ease;
        }

        .pd-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pd-thumb:hover {
            opacity: 1;
        }

        .pd-thumb.active {
            border-color: var(--bs-secondary);
            opacity: 1;
        }

        /* ---------- Info ---------- */
        .pd-category {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            background: rgba(255, 181, 36, .16);
            color: var(--bs-dark);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .pd-category:hover {
            background: var(--bs-secondary);
            color: var(--bs-dark);
        }

        .pd-title {
            font-family: Raleway, sans-serif;
            font-weight: 800;
        }

        .pd-price-row {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 6px 14px;
            margin: 18px 0 8px;
        }

        .pd-price {
            color: var(--bs-primary);
            font-family: Raleway, sans-serif;
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .pd-compare {
            color: var(--bs-gray);
            font-size: 1.1rem;
        }

        .pd-discount {
            padding: 3px 12px;
            border-radius: 999px;
            background: rgba(229, 72, 77, .12);
            color: #e5484d;
            font-size: .8rem;
            font-weight: 800;
        }

        .pd-stock {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: .9rem;
            font-weight: 700;
        }

        .pd-stock i {
            font-size: .55rem;
        }

        .pd-stock.in {
            color: #21a366;
        }

        .pd-stock.low {
            color: #d97706;
        }

        .pd-stock.out {
            color: #e5484d;
        }

        .pd-stock.neutral {
            color: var(--bs-gray);
        }

        .pd-short {
            color: var(--bs-gray);
            line-height: 1.7;
        }

        .pd-divider {
            margin: 22px 0;
            border-top: 1px solid rgba(0, 0, 0, .08);
        }

        /* ---------- Options ---------- */
        .pd-option {
            margin-bottom: 20px;
        }

        .pd-option-label {
            margin-bottom: 10px;
            font-size: .92rem;
            color: var(--bs-dark);
        }

        .pd-option-label strong {
            color: var(--bs-primary);
        }

        .pd-option-values {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pd-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 50px;
            padding: 10px 20px;
            border: 1.5px solid rgba(0, 0, 0, .15);
            border-radius: 999px;
            background: var(--bs-white);
            color: var(--bs-dark);
            font-size: .92rem;
            font-weight: 600;
            line-height: 1;
            transition: .2s ease;
        }

        .pd-chip:hover:not(:disabled):not(.is-selected) {
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }

        .pd-chip.is-selected {
            border-color: var(--bs-primary);
            background: var(--bs-primary);
            color: var(--bs-white);
            box-shadow: 0 4px 12px rgba(129, 196, 8, .35);
        }

        .pd-chip.is-soldout {
            border-style: dashed;
            text-decoration: line-through;
            color: #9aa0a6;
        }

        .pd-chip.is-selected.is-soldout {
            color: var(--bs-white);
            background: #9aa0a6;
            border-color: #9aa0a6;
            box-shadow: none;
        }

        .pd-chip:disabled {
            border-style: dashed;
            opacity: .35;
            cursor: not-allowed;
        }

        .pd-dot {
            width: 14px;
            height: 14px;
            border: 1px solid rgba(0, 0, 0, .25);
            border-radius: 50%;
        }

        /* ---------- Quantity + actions ---------- */
        .pd-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
        }

        .pd-qty {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid rgba(0, 0, 0, .15);
            border-radius: 999px;
            background: var(--bs-white);
            overflow: hidden;
        }

        .pd-qty button {
            width: 46px;
            height: 50px;
            border: 0;
            background: transparent;
            color: var(--bs-dark);
            transition: .2s ease;
        }

        .pd-qty button:hover:not(:disabled) {
            background: var(--bs-light);
            color: var(--bs-primary);
        }

        .pd-qty button:disabled {
            opacity: .35;
        }

        .pd-qty input {
            width: 56px;
            height: 50px;
            border: 0;
            outline: 0;
            background: transparent;
            text-align: center;
            font-weight: 700;
            -moz-appearance: textfield;
        }

        .pd-qty input::-webkit-outer-spin-button,
        .pd-qty input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        #pdAddToCart {
            min-width: 220px;
        }

        .pd-message {
            min-height: 24px;
            margin-top: 12px;
            font-size: .9rem;
            font-weight: 600;
        }

        .pd-message.success {
            color: #21a366;
        }

        .pd-message.error {
            color: #e5484d;
        }

        /* ---------- Meta + trust ---------- */
        .pd-meta {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 8px 16px;
            margin: 0;
            font-size: .92rem;
        }

        .pd-meta dt {
            color: var(--bs-gray);
            font-weight: 600;
        }

        .pd-meta dd {
            margin: 0;
            color: var(--bs-dark);
            font-weight: 600;
        }

        .pd-trust {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 22px;
        }

        .pd-trust-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid rgba(129, 196, 8, .18);
            border-radius: 12px;
            background: var(--bs-light);
            font-size: .82rem;
        }

        .pd-trust-item i {
            color: var(--bs-secondary);
            font-size: 1.3rem;
        }

        .pd-trust-item strong {
            display: block;
            color: var(--bs-dark);
        }

        .pd-trust-item a,
        .pd-trust-item span {
            color: var(--bs-gray);
        }

        .pd-trust-item a:hover {
            color: var(--bs-primary);
        }

        /* ---------- Tabs ---------- */
        .pd-tabs .nav-link {
            padding: 12px 22px;
            color: var(--bs-gray);
            font-weight: 700;
        }

        .pd-tabs .nav-link.active {
            color: var(--bs-primary);
        }

        .pd-description {
            color: var(--bs-gray);
            line-height: 1.85;
            overflow-wrap: anywhere;
        }

        .pd-description img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .pd-description h2,
        .pd-description h3,
        .pd-description h4 {
            margin: 20px 0 10px;
            color: var(--bs-primary);
        }

        .pd-info-table th {
            width: 200px;
            color: var(--bs-dark);
            font-weight: 700;
        }

        @media (max-width: 575px) {
            .pd-price {
                font-size: 1.7rem;
            }

            .pd-trust {
                grid-template-columns: 1fr;
            }

            .pd-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .pd-qty {
                align-self: flex-start;
            }

            #pdAddToCart {
                width: 100%;
            }

            .pd-meta {
                grid-template-columns: 90px 1fr;
            }

            .pd-info-table th {
                width: 120px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $placeholder = asset('img/fruite-item-1.jpg');
        $images = $product->images;

        // options that really have values (their order matches the order of each variant's option_values)
        $options = $product->options->filter(fn ($o) => ! empty($o->values))->values();
        $hasVariants = $product->has_variants && $product->variants->isNotEmpty() && $options->isNotEmpty();
        $unavailable = $product->has_variants && ! $hasVariants; // variants were promised but none are usable
        $lowStockAt = 5;

        $basePrice = (float) $product->price;
        $baseCompare = $product->compare_price && (float) $product->compare_price > $basePrice ? (float) $product->compare_price : null;
        $baseStock = $unavailable ? 0 : (int) $product->stock;

        $minPrice = $hasVariants ? (float) $product->variants->min('price') : $basePrice;
        $maxPrice = $hasVariants ? (float) $product->variants->max('price') : $basePrice;

        $initialStockClass = $baseStock < 1 ? 'out' : ($baseStock <= $lowStockAt ? 'low' : 'in');
        $initialStockText = $unavailable ? 'Currently unavailable' : ($baseStock < 1 ? 'Out of stock' : ($baseStock <= $lowStockAt ? "Only {$baseStock} left" : 'In stock'));

        $payload = [
            'productId' => $product->id,
            'hasVariants' => $hasVariants,
            'unavailable' => $unavailable,
            'basePrice' => $basePrice,
            'baseCompare' => $baseCompare,
            'baseStock' => $baseStock,
            'lowStock' => $lowStockAt,
            'options' => $options->map(fn ($o) => ['name' => $o->name, 'values' => array_values($o->values)])->values(),
            'variants' => $hasVariants
                ? $product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'values' => array_values($v->option_values ?? []),
                    'price' => (float) $v->price,
                    'compare' => $v->compare_price && (float) $v->compare_price > (float) $v->price ? (float) $v->compare_price : null,
                    'stock' => (int) $v->stock,
                    'sku' => $v->sku,
                ])->values()
                : [],
        ];
    @endphp

    <header class="container-fluid page-header py-5">
        <div class="container text-center py-4">
            <h1 class="text-white display-6 mb-2">{{ $product->name }}</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.shop') }}">Shop</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('frontend.shop', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
                </li>
                <li class="breadcrumb-item active text-white">{{ $product->name }}</li>
            </ol>
        </div>
    </header>

    <main>
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="row g-5">

                    {{-- GALLERY --}}
                    <div class="col-lg-6">
                        <div class="pd-gallery-main">
                            <img id="pdMainImage" src="{{ $images->first()?->url ?? $placeholder }}"
                                alt="{{ $product->name }}">
                            <span class="pd-badge" id="pdBadge" style="display:none"></span>
                        </div>

                        @if ($images->count() > 1)
                            <div class="pd-thumbs">
                                @foreach ($images as $image)
                                    <button type="button" class="pd-thumb {{ $loop->first ? 'active' : '' }}"
                                        data-image="{{ $image->url }}" aria-label="Show image {{ $loop->iteration }}">
                                        <img src="{{ $image->url }}" alt="" loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- INFO --}}
                    <div class="col-lg-6">
                        <a href="{{ route('frontend.shop', ['category' => $product->category_id]) }}"
                            class="pd-category">{{ $product->category->name }}</a>

                        <h2 class="pd-title display-6 mt-3 mb-2">{{ $product->name }}</h2>
                        <p class="text-muted mb-0">
                            Sold by
                            <a href="{{ route('frontend.shop', ['shop' => $product->shop_id]) }}"
                                class="fw-bold">{{ $product->shop->name }}</a>
                        </p>

                        <div class="pd-price-row">
                            <span class="pd-price" id="pdPrice">
                                @if ($hasVariants && $minPrice !== $maxPrice)
                                    @money($minPrice) – @money($maxPrice)
                                @else
                                    @money($minPrice)
                                @endif
                            </span>
                            <del class="pd-compare" id="pdCompare"
                                @if (! $baseCompare || $hasVariants) style="display:none" @endif>
                                @money($baseCompare ?? 0)
                            </del>
                            <span class="pd-discount" id="pdDiscount"
                                @if (! $baseCompare || $hasVariants) style="display:none" @endif>
                                @if ($baseCompare)
                                    -{{ round((1 - $basePrice / $baseCompare) * 100) }}%
                                @endif
                            </span>
                        </div>

                        <div class="pd-stock {{ $hasVariants ? 'neutral' : $initialStockClass }}" id="pdStock">
                            <i class="fas fa-circle" aria-hidden="true"></i>
                            <span id="pdStockText">{{ $hasVariants ? 'Choose your options' : $initialStockText }}</span>
                        </div>

                        @if ($product->short_description)
                            <p class="pd-short">{{ $product->short_description }}</p>
                        @endif

                        <div class="pd-divider"></div>

                        {{-- OPTIONS: every option on its own row, every value is a button --}}
                        @if ($hasVariants)
                            @foreach ($options as $i => $option)
                                <div class="pd-option">
                                    <div class="pd-option-label">
                                        {{ $option->name }}:
                                        <strong data-selected-label="{{ $i }}">Select</strong>
                                    </div>
                                    <div class="pd-option-values" role="group" aria-label="{{ $option->name }}">
                                        @foreach ($option->values as $value)
                                            <button type="button" class="pd-chip" data-option-index="{{ $i }}"
                                                data-value="{{ $value }}" aria-pressed="false">{{ $value }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- QUANTITY + ADD TO CART --}}
                        <div class="pd-actions">
                            <div class="pd-qty">
                                <button type="button" data-qty-step="-1" aria-label="Decrease quantity"><i
                                        class="fas fa-minus"></i></button>
                                <input type="number" id="pdQty" value="1" min="1" max="1"
                                    inputmode="numeric" aria-label="Quantity">
                                <button type="button" data-qty-step="1" aria-label="Increase quantity"><i
                                        class="fas fa-plus"></i></button>
                            </div>

                            <button type="button" id="pdAddToCart" class="btn btn-primary rounded-pill px-5 py-3">
                                <i class="fa fa-shopping-bag me-2" aria-hidden="true"></i><span id="pdAddLabel">Add to
                                    cart</span>
                            </button>
                        </div>
                        <div class="pd-message" id="pdMessage" aria-live="polite"></div>

                        <div class="pd-divider"></div>

                        <dl class="pd-meta">
                            @if ($hasVariants)
                                <dt>SKU</dt>
                                <dd id="pdSku">—</dd>
                            @endif
                            <dt>Category</dt>
                            <dd>{{ $product->category->name }}</dd>
                            <dt>Shop</dt>
                            <dd>{{ $product->shop->name }}</dd>
                        </dl>

                        <div class="pd-trust">
                            <div class="pd-trust-item">
                                <i class="fas fa-truck" aria-hidden="true"></i>
                                <div>
                                    <strong>Delivery</strong>
                                    <a href="{{ route('frontend.delivery-policy') }}">Delivery policy</a>
                                </div>
                            </div>
                            <div class="pd-trust-item">
                                <i class="fas fa-undo-alt" aria-hidden="true"></i>
                                <div>
                                    <strong>Returns</strong>
                                    <a href="{{ route('frontend.refund-policy') }}">Refund policy</a>
                                </div>
                            </div>
                            <div class="pd-trust-item">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                <div>
                                    <strong>Secure</strong>
                                    <span>Safe checkout</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TABS --}}
                    <div class="col-12">
                        <ul class="nav nav-tabs pd-tabs mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-description" data-bs-toggle="tab"
                                    data-bs-target="#pane-description" type="button" role="tab">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-info" data-bs-toggle="tab"
                                    data-bs-target="#pane-info" type="button" role="tab">Additional
                                    information</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pane-description" role="tabpanel">
                                @php $descriptionHtml = \App\Support\HtmlSanitizer::clean($product->description); @endphp
                                @if ($descriptionHtml !== '')
                                    <div class="pd-description">{!! $descriptionHtml !!}</div>
                                @else
                                    <p class="text-muted mb-0">The seller has not added a description yet.</p>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="pane-info" role="tabpanel">
                                <table class="table table-borderless pd-info-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $product->category->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shop</th>
                                            <td>{{ $product->shop->name }}</td>
                                        </tr>
                                        @foreach ($options as $option)
                                            <tr>
                                                <th>{{ $option->name }}</th>
                                                <td>{{ implode(', ', $option->values) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section class="container-fluid py-5 bg-light bazaar-section">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h2 class="display-6 mb-0">More in {{ $product->category->name }}</h2>
                        <a href="{{ route('frontend.shop', ['category' => $product->category_id]) }}"
                            class="btn btn-outline-primary rounded-pill px-4">View all</a>
                    </div>
                    <div class="row g-4">
                        @foreach ($relatedProducts as $relatedProduct)
                            <div class="col-sm-6 col-lg-3">
                                @include('frontend.partials.product-card', ['product' => $relatedProduct])
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection

@section('customjs')
    <script>
        (function() {
            var D = {{ \Illuminate\Support\Js::from($payload) }};

            var el = {
                main: document.getElementById('pdMainImage'),
                badge: document.getElementById('pdBadge'),
                price: document.getElementById('pdPrice'),
                compare: document.getElementById('pdCompare'),
                discount: document.getElementById('pdDiscount'),
                stock: document.getElementById('pdStock'),
                stockText: document.getElementById('pdStockText'),
                sku: document.getElementById('pdSku'),
                qty: document.getElementById('pdQty'),
                add: document.getElementById('pdAddToCart'),
                addLabel: document.getElementById('pdAddLabel'),
                message: document.getElementById('pdMessage')
            };
            var chips = Array.prototype.slice.call(document.querySelectorAll('.pd-chip'));
            var stepButtons = Array.prototype.slice.call(document.querySelectorAll('[data-qty-step]'));
            var selected = D.options.map(function() {
                return null;
            });
            var busy = false;

            function money(n) {
                return window.BazaarMoney(n); // same currency format as the rest of the site
            }

            /* ---------- gallery ---------- */
            Array.prototype.forEach.call(document.querySelectorAll('.pd-thumb'), function(thumb) {
                thumb.addEventListener('click', function() {
                    if (el.main.getAttribute('src') === thumb.dataset.image) return;

                    el.main.classList.add('is-changing');
                    setTimeout(function() {
                        el.main.src = thumb.dataset.image;
                        el.main.classList.remove('is-changing');
                    }, 150);

                    Array.prototype.forEach.call(document.querySelectorAll('.pd-thumb'), function(t) {
                        t.classList.remove('active');
                    });
                    thumb.classList.add('active');
                });
            });

            /* ---------- variant logic ---------- */
            function matches(variant, sel) {
                return sel.every(function(s, k) {
                    return s === null || variant.values[k] === s;
                });
            }

            function compatible(sel) {
                return D.variants.filter(function(v) {
                    return matches(v, sel);
                });
            }

            function exactVariant() {
                if (selected.some(function(s) {
                        return s === null;
                    })) return null;
                return D.variants.find(function(v) {
                    return matches(v, selected);
                }) || null;
            }

            // the thing being bought: the chosen variant, or the product itself when it has no options
            function current() {
                if (D.hasVariants) return exactVariant();
                if (D.unavailable) return null;
                return {
                    id: null,
                    price: D.basePrice,
                    compare: D.baseCompare,
                    stock: D.baseStock,
                    sku: null
                };
            }

            function selectValue(index, value) {
                selected[index] = value;

                // this value does not exist together with the others: keep only the new choice
                if (!compatible(selected).length) {
                    selected = selected.map(function(s, k) {
                        return k === index ? value : null;
                    });
                }
                render();
            }

            chips.forEach(function(chip) {
                var optionName = D.options[+chip.dataset.optionIndex].name;
                var color = chip.dataset.value.replace(/\s+/g, '');

                // colour options get a small colour dot when the value is a real colour name
                if (/colou?r/i.test(optionName) && window.CSS && CSS.supports('color', color)) {
                    var dot = document.createElement('span');
                    dot.className = 'pd-dot';
                    dot.style.background = color;
                    chip.insertBefore(dot, chip.firstChild);
                }

                chip.addEventListener('click', function() {
                    selectValue(+chip.dataset.optionIndex, chip.dataset.value);
                });
            });

            /* ---------- rendering ---------- */
            function refreshChips() {
                chips.forEach(function(chip) {
                    var i = +chip.dataset.optionIndex;
                    var value = chip.dataset.value;
                    var test = selected.slice();
                    test[i] = value;

                    var list = compatible(test);
                    var exists = list.length > 0;
                    var inStock = list.some(function(v) {
                        return v.stock > 0;
                    });
                    var isSelected = selected[i] === value;

                    chip.classList.toggle('is-selected', isSelected);
                    chip.classList.toggle('is-soldout', exists && !inStock);
                    chip.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                    chip.disabled = !exists;
                    chip.title = !exists ? 'Not available with your current choice' : (!inStock ? 'Sold out' : '');
                });

                Array.prototype.forEach.call(document.querySelectorAll('[data-selected-label]'), function(label) {
                    label.textContent = selected[+label.dataset.selectedLabel] || 'Select';
                });
            }

            function setStock(cls, text) {
                el.stock.className = 'pd-stock ' + cls;
                el.stockText.textContent = text;
            }

            function setPrice(item) {
                el.price.textContent = money(item.price);

                if (item.compare && item.compare > item.price) {
                    el.compare.textContent = money(item.compare);
                    el.compare.style.display = '';
                    el.discount.textContent = '-' + Math.round((1 - item.price / item.compare) * 100) + '%';
                    el.discount.style.display = '';
                } else {
                    el.compare.style.display = 'none';
                    el.discount.style.display = 'none';
                }
            }

            function setBadge(item) {
                if (item && item.stock < 1) {
                    el.badge.textContent = 'Sold out';
                    el.badge.className = 'pd-badge pd-badge-out';
                    el.badge.style.display = '';
                } else if (item && item.compare && item.compare > item.price) {
                    el.badge.textContent = 'Sale';
                    el.badge.className = 'pd-badge pd-badge-sale';
                    el.badge.style.display = '';
                } else {
                    el.badge.style.display = 'none';
                }
            }

            function maxQty() {
                var item = current();
                return item && item.stock > 0 ? Math.min(99, item.stock) : 1;
            }

            function clampQty() {
                var q = parseInt(el.qty.value, 10);
                if (isNaN(q) || q < 1) q = 1;
                q = Math.min(q, maxQty());
                el.qty.value = q;
                return q;
            }

            function setAddButton(enabled, label) {
                el.add.disabled = !enabled || busy;
                el.addLabel.textContent = label;
            }

            function render() {
                refreshChips();
                var item = current();

                if (D.hasVariants && !item) {
                    // not every option is chosen yet: show the price range of what is still possible
                    var pool = compatible(selected);
                    var prices = pool.map(function(v) {
                        return v.price;
                    });
                    var lo = Math.min.apply(null, prices);
                    var hi = Math.max.apply(null, prices);

                    el.price.textContent = lo === hi ? money(lo) : money(lo) + ' – ' + money(hi);
                    el.compare.style.display = 'none';
                    el.discount.style.display = 'none';

                    var missing = D.options.filter(function(o, k) {
                        return selected[k] === null;
                    }).map(function(o) {
                        return o.name;
                    });

                    setStock('neutral', 'Choose your ' + missing.join(' and ').toLowerCase());
                    setBadge(null);
                    if (el.sku) el.sku.textContent = '—';
                    setAddButton(false, 'Select ' + missing[0]);
                } else if (!item) {
                    setStock('out', 'Currently unavailable');
                    setBadge(null);
                    setAddButton(false, 'Unavailable');
                } else {
                    setPrice(item);
                    setBadge(item);
                    if (el.sku) el.sku.textContent = item.sku || '—';

                    if (item.stock < 1) {
                        setStock('out', 'Out of stock');
                        setAddButton(false, 'Out of stock');
                    } else if (item.stock <= D.lowStock) {
                        setStock('low', 'Only ' + item.stock + ' left');
                        setAddButton(true, 'Add to cart');
                    } else {
                        setStock('in', 'In stock');
                        setAddButton(true, 'Add to cart');
                    }
                }

                var max = maxQty();
                el.qty.max = max;
                clampQty();
                var noStock = !item || item.stock < 1;
                el.qty.disabled = noStock;
                stepButtons.forEach(function(b) {
                    b.disabled = noStock;
                });
            }

            /* ---------- quantity ---------- */
            stepButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    el.qty.value = (parseInt(el.qty.value, 10) || 1) + parseInt(button.dataset.qtyStep, 10);
                    clampQty();
                });
            });
            el.qty.addEventListener('change', clampQty);
            el.qty.addEventListener('blur', clampQty);

            /* ---------- add to cart (PHP session) ---------- */
            function say(text, type) {
                el.message.textContent = text;
                el.message.className = 'pd-message ' + (type || '');
            }

            el.add.addEventListener('click', function() {
                var item = current();
                if (!item || item.stock < 1 || busy) return;

                var quantity = clampQty();
                busy = true;
                el.add.disabled = true;
                el.addLabel.textContent = 'Adding...';
                say('');

                window.BazaarCart.add({
                        product_id: D.productId,
                        variant_id: item.id,
                        quantity: quantity
                    })
                    .then(function() {
                        say('Added to your cart.', 'success');
                    })
                    .catch(function(error) {
                        say(error.message, 'error');
                        window.BazaarCart.toast(error.message, 'error');
                    })
                    .then(function() {
                        busy = false;
                        render();
                    });
            });

            /* ---------- start ---------- */
            if (D.hasVariants) {
                // start on the first option set that is in stock, so the price and button are ready
                var first = D.variants.find(function(v) {
                    return v.stock > 0;
                }) || D.variants[0];

                if (first) selected = first.values.slice(0, D.options.length);
            }

            render();
        })();
    </script>
@endsection
