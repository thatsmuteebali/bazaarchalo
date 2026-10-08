@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <header class="container-fluid page-header py-5">
        <div class="container text-center py-5">
            <p class="text-secondary fw-bold text-uppercase mb-2">
                Find your favourites
            </p>
            <h1 class="text-white display-5 mb-3">Products Categories</h1>
            <p class="text-white mb-0">
                Fresh picks and everyday essentials from trusted local sellers.
            </p>
            <ol class="breadcrumb justify-content-center mt-4 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Categories</li>
            </ol>
        </div>
    </header>

    <main>
        <section class="container-fluid py-5 bazaar-section">
            <div class="container py-4">
                <div class="category-listing-intro d-flex flex-wrap justify-content-between align-items-end mb-5">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Curated for your kitchen
                        </p>
                        <h2 class="display-6 mb-2">Everything fresh, all in one place</h2>
                        <p class="text-muted mb-0">
                            Choose a category to discover products available from shops near
                            you.
                        </p>
                    </div>
                    <a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill px-4 mt-3 mt-lg-0">Browse all
                        products <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="row g-4">
                    @if (count($categories) > 0)
                        @foreach ($categories as $category)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ route('frontend.shop', ['category' => $category->id]) }}" class="directory-card"><img
                                    src="{{ $category->image ? $category->image_url : asset('img/fruite-item-1.jpg') }}" alt="{{ $category->name }}" />
                                    <span>
                                        <strong>{{$category->name}}</strong>
                                        <small>{{ $category->products_count }} products</small>
                                    </span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
        <section class="container-fluid py-5 bg-light">
            <div class="container py-4">
                <div
                    class="directory-banner rounded p-4 p-lg-5 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary fw-bold text-uppercase mb-2">
                            Local is always in season
                        </p>
                        <h2 class="mb-2">Good food starts close to home.</h2>
                        <p class="mb-0 text-muted">
                            Explore nearby sellers and support the people who make your
                            neighbourhood special.
                        </p>
                    </div>
                    <a href="{{ route('frontend.shop-listing') }}"
                        class="btn btn-primary rounded-pill px-4 mt-4 mt-lg-0">Explore local
                        shops <i class="fas fa-store ms-2"></i></a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
