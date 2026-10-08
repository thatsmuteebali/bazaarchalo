@extends('layouts.app')
@section('content')
    <header class="container-fluid page-header py-5">
        <div class="container text-center py-4">
            <h1 class="text-white display-6 mb-2">{{ $product->name }}</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.shop') }}">Shop</a></li>
                <li class="breadcrumb-item active text-white">{{ $product->name }}</li>
            </ol>
        </div>
    </header>

    <main>
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="row g-5">
                    <div class="col-lg-6">
                        @php($cover = $product->images->first())
                        <img id="productDetailImage" src="{{ $cover?->url ?? asset('img/fruite-item-1.jpg') }}"
                            class="img-fluid w-100 rounded" alt="{{ $product->name }}" />
                        @if ($product->images->count() > 1)
                            <div class="d-flex gap-2 mt-3 flex-wrap">
                                @foreach ($product->images as $image)
                                    <button type="button" class="border-0 bg-transparent p-0" data-product-image="{{ $image->url }}"
                                        aria-label="View image {{ $loop->iteration }}">
                                        <img src="{{ $image->url }}" alt="" class="rounded"
                                            style="width: 76px; height: 76px; object-fit: cover" />
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <p class="text-secondary fw-bold text-uppercase mb-2">{{ $product->category->name }}</p>
                        <h2 class="display-6 mb-2">{{ $product->name }}</h2>
                        <p class="text-muted mb-3">Sold by <a href="{{ route('frontend.shop', ['shop' => $product->shop_id]) }}">{{ $product->shop->name }}</a></p>
                        @if ($product->short_description)
                            <p class="lead">{{ $product->short_description }}</p>
                        @endif
                        <p id="detailPrice" class="h3 fw-bold text-primary mb-4">
                            @if ($product->has_variants)
                                From ${{ number_format((float) $product->variants->min('price'), 2) }}
                            @else
                                ${{ number_format((float) $product->price, 2) }}
                            @endif
                        </p>

                        <form id="productDetailCartForm" data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}" data-product-image="{{ $cover?->url ?? asset('img/fruite-item-1.jpg') }}"
                            data-product-price="{{ $product->price }}" data-product-stock="{{ $product->stock }}"
                            data-has-variants="{{ $product->has_variants ? '1' : '0' }}">
                            @if ($product->has_variants)
                                <div class="mb-3">
                                    <label for="productVariant" class="form-label">Choose an option</label>
                                    <select id="productVariant" class="form-select" required>
                                        <option value="">Select a variant</option>
                                        @foreach ($product->variants as $variant)
                                            <option value="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                                data-stock="{{ $variant->stock }}" data-title="{{ $variant->title }}"
                                                @disabled($variant->stock < 1)>
                                                {{ $variant->title }} - ${{ number_format((float) $variant->price, 2) }}
                                                @if ($variant->stock < 1) (Out of stock) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <p class="text-muted">{{ $product->stock }} in stock</p>
                            @endif

                            <div class="d-flex align-items-center gap-3 mb-4">
                                <label for="detailQuantity" class="form-label mb-0">Quantity</label>
                                <input id="detailQuantity" type="number" class="form-control" value="1" min="1"
                                    max="{{ $product->has_variants ? 1 : max(1, $product->stock) }}" style="max-width: 110px"
                                    required />
                            </div>
                            <button id="detailAddToCart" type="submit" class="btn btn-primary rounded-pill px-4 py-3"
                                @disabled($product->has_variants || $product->stock < 1)>
                                <i class="fa fa-shopping-bag me-2" aria-hidden="true"></i>Add to cart
                            </button>
                            <span id="variantStockMessage" class="ms-3 text-muted" aria-live="polite"></span>
                        </form>
                    </div>
                    @if ($product->description)
                        <div class="col-12">
                            <h3 class="h4 mb-3">Product details</h3>
                            <div class="text-muted">{!! nl2br(e($product->description)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section class="container-fluid py-5 bg-light bazaar-section">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h2 class="display-6 mb-0">More in {{ $product->category->name }}</h2>
                        <a href="{{ route('frontend.shop', ['category' => $product->category_id]) }}" class="btn btn-outline-primary rounded-pill px-4">View all</a>
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
