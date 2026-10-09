@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <header class="container-fluid page-header py-5">
        <div class="container text-center py-5">
            <p class="text-secondary fw-bold text-uppercase mb-2">
                Fresh from your neighbourhood
            </p>
            <h1 class="text-white display-5 mb-3">Popular Shops Near You</h1>
            <p class="text-white mb-0">
                Meet the local sellers behind the products you love.
            </p>
            <ol class="breadcrumb justify-content-center mt-4 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Shops</li>
            </ol>
        </div>
    </header>

    <main>
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Your local marketplace
                        </p>
                        <h2 class="display-6 mb-2">Find a shop you can trust</h2>
                        <p class="text-muted mb-0">
                            Fresh produce and pantry essentials, delivered from sellers near
                            you.
                        </p>
                    </div>
                    <span class="shop-result-count mt-3 mt-lg-0"><i
                            class="fas fa-store text-secondary me-2"></i>{{ $shops->total() }} shops found</span>
                </div>
                <div class="shop-directory-toolbar bg-light rounded p-3 mb-5">
                    <form action="{{ route('frontend.shop-listing') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="shopSearch" class="form-label">Search shops</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0"><i
                                        class="fas fa-search text-primary"></i></span><input id="shopSearch" type="search" name="q"
                                    value="{{ $filters['search'] }}" class="form-control border-0"
                                    placeholder="Shop name or description" aria-label="Search shops" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="shopCategory" class="form-label">Product category</label>
                            <select id="shopCategory" name="category" class="form-select">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($filters['category'] == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" aria-label="Apply filters">
                                <i class="fas fa-filter" aria-hidden="true"></i>
                            </button>
                            <a href="{{ route('frontend.shop-listing') }}" class="btn btn-outline-secondary" aria-label="Clear filters">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </a>
                        </div>
                    </form>
                </div>
                <div class="row g-4">
                    @forelse ($shops as $shop)
                            <div class="col-md-6 col-lg-4">
                                <article class="seller-directory-card h-100">
                                    <div class="seller-directory-head">
                                        <img src="{{ $shop->banner_url }}" alt="{{ $shop->name }}"
                                            class="shop-avatar rounded-circle" /><span class="seller-status"><i
                                                class="fas fa-circle"></i> Open now</span>
                                    </div>
                                    <h4 class="mt-4 mb-2">{{ $shop->name }}</h4>
                                    <p class="text-muted mb-3">
                                        {{ $shop->description }}
                                    </p>
                                    <div class="seller-meta">
                                        <span><i class="fas fa-box-open text-secondary me-1"></i>{{ $shop->products_count }} active products</span>
                                    </div>
                                    <a href="{{ route('frontend.shop', ['shop' => $shop->id]) }}"
                                        class="btn btn-primary rounded-pill w-100 mt-4">Visit shop <i
                                            class="fas fa-arrow-right ms-2"></i></a>
                                </article>
                            </div>
                    @empty
                        <div class="col-12"><p class="alert alert-light border mb-0">No shops match these filters.</p></div>
                    @endforelse
                    <div class="col-12 d-flex justify-content-center mt-4">
                        {{ $shops->links() }}
                    </div>
                </div>
            </div>
        </section>
        <section class="container-fluid py-5 bg-light">
            <div class="container py-4">
                <div
                    class="directory-banner rounded p-4 p-lg-5 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Are you a local seller?
                        </p>
                        <h2 class="mb-2">Bring your shop online.</h2>
                        <p class="mb-0 text-muted">
                            Reach more nearby customers and grow with Bazaar Chalo.
                        </p>
                    </div>
                    <a href="{{ route('seller.register') }}" class="btn btn-primary rounded-pill px-4 mt-4 mt-lg-0">Register
                        your shop <i class="fas fa-store ms-2"></i></a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
