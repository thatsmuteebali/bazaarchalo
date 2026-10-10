@extends('layouts.app')
@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
        </ol>
    </div>

    <div class="container-fluid fruite py-5">
        <div class="container py-4">
            <div class="shop-page-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <p class="text-secondary fw-bold text-uppercase mb-2">Local marketplace</p>
                    <h1 class="display-6 mb-1">Shop Products</h1>
                    <p class="text-muted mb-0">Discover products from active local shops.</p>
                </div>
                <button type="button" class="btn btn-outline-primary d-lg-none" id="shopFiltersToggle"
                    aria-controls="shopFilterPanel" aria-expanded="false">
                    <i class="fas fa-sliders-h me-2" aria-hidden="true"></i>Filters
                </button>
            </div>

            <div class="shop-results-toolbar mb-4">
                <div class="row g-3 align-items-center">

                    <div class="col-12 col-md-9 shop-search-row">
                        <div class="input-group input-group-md pt-md-2">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"
                                    aria-hidden="true"></i></span>
                            <input id="shopSearch" type="search" name="q" form="shopFilterForm"
                                value="{{ $filters['search'] }}" class="form-control"
                                placeholder="Search products by name or description" aria-label="Search products" />
                            <button type="submit" form="shopFilterForm" class="btn btn-primary px-4">Search</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mt-md-0">
                        <label for="sortProducts" class="shop-results-sort mb-0">
                            <span class="text-muted text-nowrap">Sort by</span>
                        </label>
                        <select id="sortProducts" name="sort" form="shopFilterForm"
                            class="form-select form-select-md" onchange="document.getElementById('shopFilterForm').submit()">
                            <option value="latest" @selected($filters['sort'] === 'latest')>Newest</option>
                            <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price: high to low</option>
                            <option value="name" @selected($filters['sort'] === 'name')>Name</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <p class="shop-results-count text-muted mb-0">{{ $products->total() }} products found</p>
                    </div>
                </div>
            </div>

            <div class="shop-filter-backdrop" id="shopFilterBackdrop"></div>
            <div class="row g-4">
                <aside class="col-lg-3 shop-filter-panel" id="shopFilterPanel" aria-label="Product filters">
                    <div class="shop-filter-panel-header d-flex justify-content-between align-items-center d-lg-none mb-3">
                        <h2 class="h5 mb-0">Filters</h2>
                        <button type="button" class="btn btn-sm btn-light" id="shopFiltersClose"
                            aria-label="Close filters">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form id="shopFilterForm" action="{{ route('frontend.shop') }}" method="GET" class="shop-filter-form">
                        <fieldset class="shop-filter-section">
                            <legend class="shop-filter-label">Categories</legend>
                            <div class="shop-filter-options">
                                @forelse ($categories as $category)
                                    <label class="shop-filter-option">
                                        <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                            @checked(in_array((int) $category->id, $filters['categoryIds'], true)) />
                                        <span>{{ $category->name }}</span>
                                        <small>{{ $category->products_count }}</small>
                                    </label>
                                @empty
                                    <p class="text-muted small mb-0">No categories available.</p>
                                @endforelse
                            </div>
                        </fieldset>

                        <fieldset class="shop-filter-section">
                            <legend class="shop-filter-label">Shops</legend>
                            <div class="shop-filter-options">
                                @forelse ($shops as $shop)
                                    <label class="shop-filter-option">
                                        <input type="checkbox" name="shop[]" value="{{ $shop->id }}"
                                            @checked(in_array((int) $shop->id, $filters['shopIds'], true)) />
                                        <span>{{ $shop->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-muted small mb-0">No shops available.</p>
                                @endforelse
                            </div>
                        </fieldset>

                        <div class="shop-filter-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="shop-filter-label mb-0">Price range</span>
                                <span class="shop-price-values small">
                                    <output id="minPriceOutput" for="minPrice">Rs.{{ number_format($filters['minPrice'], 0) }}</output>
                                    <span aria-hidden="true">-</span>
                                    <output id="maxPriceOutput" for="maxPrice">Rs.{{ number_format($filters['maxPrice'], 0) }}</output>
                                </span>
                            </div>
                            <div class="shop-price-slider" id="shopPriceSlider"
                                data-price-limit="{{ $filters['priceLimit'] }}">
                                <div class="shop-price-track" aria-hidden="true"></div>
                                <div class="shop-price-selection" id="shopPriceSelection" aria-hidden="true"></div>
                                <input id="minPrice" type="range" name="min_price" min="0"
                                    max="{{ $filters['priceLimit'] }}" step="1" value="{{ $filters['minPrice'] }}"
                                    aria-label="Minimum price" />
                                <input id="maxPrice" type="range" name="max_price" min="0"
                                    max="{{ $filters['priceLimit'] }}" step="1" value="{{ $filters['maxPrice'] }}"
                                    aria-label="Maximum price" />
                            </div>
                        </div>

                        <div class="shop-filter-actions d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-2" aria-hidden="true"></i>Filter
                            </button>
                            <a href="{{ route('frontend.shop') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>

                    @if ($featuredProducts->isNotEmpty())
                        <section class="shop-featured-section" aria-labelledby="shopFeaturedHeading">
                            <h2 class="shop-filter-label" id="shopFeaturedHeading">Featured products</h2>
                            <div class="shop-featured-list">
                                @foreach ($featuredProducts as $featuredProduct)
                                    @php
                                        $featuredImage =
                                            $featuredProduct->coverImage?->url ?? asset('img/fruite-item-1.jpg');
                                        $featuredPrice = $featuredProduct->has_variants
                                            ? $featuredProduct->variants_min_price ??
                                                ($featuredProduct->variants->min('price') ?? $featuredProduct->price)
                                            : $featuredProduct->price;
                                    @endphp
                                    <a class="shop-featured-item"
                                        href="{{ route('frontend.product-detail', $featuredProduct) }}">
                                        <img src="{{ $featuredImage }}" alt="{{ $featuredProduct->name }}" />
                                        <span>
                                            <strong>{{ $featuredProduct->name }}</strong>
                                            <small>Rs.{{ number_format((float) $featuredPrice, 2) }}</small>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>

                <section class="col-lg-9" aria-label="Products">
                    <div class="row g-4">
                        @forelse ($products as $product)
                            <div class="col-md-6 col-xl-4">
                                @include('frontend.partials.product-card', ['product' => $product])
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border">No products match these filters.</div>
                            </div>
                        @endforelse

                        @if ($products->hasPages())
                            <div class="col-12">
                                <div class="pagination d-flex justify-content-center mt-5">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
