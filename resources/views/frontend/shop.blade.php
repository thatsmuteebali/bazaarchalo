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
        <div class="container py-5">
            <h1 class="mb-4">Fresh Products Shop</h1>

            <div class="row g-4">
                <div class="col-xl-3">
                    <div class="input-group w-100 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1" />
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
                <div class="col-6"></div>
                <div class="col-xl-3">
                    <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                        <label for="fruits">Default Sorting:</label>
                        <select id="fruits" name="fruitlist" class="border-0 form-select-sm bg-light me-3" form="fruitform">
                            <option value="volvo">Nothing</option>
                            <option value="saab">Popularity</option>
                            <option value="opel">Organic</option>
                            <option value="audi">Fantastic</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h4>Categories</h4>
                            <ul class="list-unstyled fruite-categorie">
                                <li>
                                    <div class="d-flex justify-content-between fruite-name">
                                        <a href="#"><i class="fas fa-apple-alt me-2"></i>Apples</a><span>(3)</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between fruite-name">
                                        <a href="#"><i class="fas fa-apple-alt me-2"></i>Oranges</a><span>(5)</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between fruite-name">
                                        <a href="#"><i class="fas fa-apple-alt me-2"></i>Strawberry</a><span>(2)</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between fruite-name">
                                        <a href="#"><i class="fas fa-apple-alt me-2"></i>Banana</a><span>(8)</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between fruite-name">
                                        <a href="#"><i class="fas fa-apple-alt me-2"></i>Pumpkin</a><span>(5)</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-12">
                            <h4 class="mb-2">Price</h4>
                            <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput" min="0" max="500" value="0" oninput="amount.value = rangeInput.value" />
                            <output id="amount" name="amount" for="rangeInput">0</output>
                        </div>

                        <div class="col-lg-12">
                            <h4>Additional</h4>
                            <div class="mb-2"><input type="radio" class="me-2" id="Categories-1" name="Categories-1" /><label for="Categories-1">Organic</label></div>
                            <div class="mb-2"><input type="radio" class="me-2" id="Categories-2" name="Categories-1" /><label for="Categories-2">Fresh</label></div>
                            <div class="mb-2"><input type="radio" class="me-2" id="Categories-3" name="Categories-1" /><label for="Categories-3">Sales</label></div>
                            <div class="mb-2"><input type="radio" class="me-2" id="Categories-4" name="Categories-1" /><label for="Categories-4">Discount</label></div>
                            <div class="mb-2"><input type="radio" class="me-2" id="Categories-5" name="Categories-1" /><label for="Categories-5">Expired</label></div>
                        </div>

                        <div class="col-lg-12">
                            <h4 class="mb-3">Featured Products</h4>
                            @foreach ([
                                ['img' => 'featur-1.jpg', 'name' => 'Big Banana'],
                                ['img' => 'featur-2.jpg', 'name' => 'Big Banana'],
                                ['img' => 'featur-3.jpg', 'name' => 'Big Banana'],
                            ] as $item)
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ asset('img/' . $item['img']) }}" class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover" alt="{{ $item['name'] }}" />
                                    <div>
                                        <a href="{{ route('frontend.product-detail') }}"><h6 class="mb-1">{{ $item['name'] }}</h6></a>
                                        <div class="product-rating mb-1">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2">$2.99</span>
                                            <span class="text-danger text-decoration-line-through">$4.11</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <a href="#" class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">View More</a>
                        </div>

                        <div class="col-lg-12">
                            <div class="position-relative">
                                <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid w-100 rounded" alt="Fresh fruits banner" />
                                <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%)">
                                    <h3 class="text-secondary fw-bold">Fresh<br />Fruits<br />Banner</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="row g-4">
                        @foreach ([
                            ['name' => 'Grapes', 'img' => 'fruite-item-5.jpg', 'hover' => 'best-product-1.jpg', 'price' => '4.99', 'badge' => null],
                            ['name' => 'Raspberries', 'img' => 'fruite-item-2.jpg', 'hover' => 'best-product-2.jpg', 'price' => '4.99', 'badge' => 'New'],
                            ['name' => 'Apricots', 'img' => 'fruite-item-4.jpg', 'hover' => 'best-product-3.jpg', 'price' => '4.99', 'badge' => null],
                            ['name' => 'Banana', 'img' => 'fruite-item-3.jpg', 'hover' => 'best-product-4.jpg', 'price' => '4.99', 'badge' => null],
                            ['name' => 'Fresh Oranges', 'img' => 'fruite-item-1.jpg', 'hover' => 'best-product-1.jpg', 'price' => '4.99', 'badge' => 'New'],
                            ['name' => 'Bell Peppers', 'img' => 'vegetable-item-4.jpg', 'hover' => 'best-product-2.jpg', 'price' => '7.99', 'badge' => '-15%'],
                            ['name' => 'Seedless Grapes', 'img' => 'fruite-item-5.jpg', 'hover' => 'best-product-3.jpg', 'price' => '5.49', 'badge' => null],
                            ['name' => 'Farm Potatoes', 'img' => 'vegetable-item-5.jpg', 'hover' => 'best-product-4.jpg', 'price' => '3.99', 'badge' => null],
                            ['name' => 'Strawberries', 'img' => 'fruite-item-2.jpg', 'hover' => 'best-product-1.jpg', 'price' => '4.49', 'badge' => null],
                        ] as $product)
                            <div class="col-md-6 col-lg-6 col-xl-4">
                                <div class="product-card">
                                    @if ($product['badge'])
                                        <span class="product-badge {{ $product['badge'] === 'New' ? 'product-badge-new' : 'product-badge-sale' }}">{{ $product['badge'] }}</span>
                                    @endif
                                    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                                        <i class="far fa-heart" aria-hidden="true"></i>
                                    </button>
                                    <div class="product-media">
                                        <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View {{ $product['name'] }}">
                                            <img src="{{ asset('img/' . $product['img']) }}" class="product-image product-image-primary" alt="{{ $product['name'] }}" />
                                            <img src="{{ asset('img/' . $product['hover']) }}" class="product-image product-image-secondary" alt="" aria-hidden="true" />
                                        </a>
                                        <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag" aria-hidden="true"></i> Add to Cart</a>
                                    </div>
                                    <div class="product-body">
                                        <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">{{ $product['name'] }}</a>
                                        <div class="product-meta">
                                            <div class="product-rating">
                                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                            </div>
                                            <div class="product-price">
                                                <span class="product-price-current">${{ $product['price'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-12">
                            <div class="pagination d-flex justify-content-center mt-5">
                                <a href="#" class="rounded">&laquo;</a>
                                <a href="#" class="active rounded">1</a>
                                <a href="#" class="rounded">2</a>
                                <a href="#" class="rounded">3</a>
                                <a href="#" class="rounded">4</a>
                                <a href="#" class="rounded">5</a>
                                <a href="#" class="rounded">6</a>
                                <a href="#" class="rounded">&raquo;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
