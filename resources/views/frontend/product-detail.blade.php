@extends('layouts.app')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Product Detail</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Product Detail</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Single Product Start -->
    <div class="container-fluid pt-5 mt-5">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                <div class="col-lg-8 col-xl-9">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded">
                                <a href="#">
                                    <img src="{{ asset('img/single-item.jpg') }}" class="img-fluid rounded" alt="Image" />
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="fw-bold mb-3">Brocoli</h4>
                            <p class="mb-3">Category: Vegetables</p>
                            <h5 class="fw-bold mb-3">3,35 $</h5>
                            <div class="d-flex mb-4">
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="mb-4">
                                The generated Lorem Ipsum is therefore always free from
                                repetition injected humour, or non-characteristic words etc.
                            </p>
                            <p class="mb-4">
                                Susp endisse ultricies nisi vel quam suscipit. Sabertooth
                                peacock flounder; chain pickerel hatchetfish, pencilfish
                                snailfish
                            </p>
                            <div class="input-group quantity mb-5" style="width: 100px">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control form-control-sm text-center border-0"
                                    value="1" />
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <a href="#"
                                class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary"><i
                                    class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                cart</a>
                        </div>
                        <div class="col-lg-12">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link active border-white border-bottom-0" type="button"
                                        role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                        aria-controls="nav-about" aria-selected="true">
                                        Description
                                    </button>
                                    <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                        id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                        aria-controls="nav-mission" aria-selected="false">
                                        Reviews
                                    </button>
                                </div>
                            </nav>
                            <div class="tab-content mb-5">
                                <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                    <p>
                                        The generated Lorem Ipsum is therefore always free from
                                        repetition injected humour, or non-characteristic words
                                        etc. Susp endisse ultricies nisi vel quam suscipit
                                    </p>
                                    <p>
                                        Sabertooth peacock flounder; chain pickerel hatchetfish,
                                        pencilfish snailfish filefish Antarctic icefish goldeye
                                        aholehole trumpetfish pilot fish airbreathing catfish,
                                        electric ray sweeper.
                                    </p>
                                    <div class="px-2">
                                        <div class="row g-4">
                                            <div class="col-6">
                                                <div
                                                    class="row bg-light align-items-center text-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Weight</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">1 kg</p>
                                                    </div>
                                                </div>
                                                <div class="row text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Country of Origin</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">Agro Farm</p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="row bg-light text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Quality</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">Organic</p>
                                                    </div>
                                                </div>
                                                <div class="row text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Сheck</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">Healthy</p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="row bg-light text-center align-items-center justify-content-center py-2">
                                                    <div class="col-6">
                                                        <p class="mb-0">Min Weight</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-0">250 Kg</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                    <div class="d-flex">
                                        <img src="{{ asset('img/avatar.jpg') }}" class="img-fluid rounded-circle p-3"
                                            style="width: 100px; height: 100px" alt="" />
                                        <div class="">
                                            <p class="mb-2" style="font-size: 14px">
                                                April 12, 2024
                                            </p>
                                            <div class="d-flex justify-content-between">
                                                <h5>Jason Smith</h5>
                                                <div class="d-flex mb-3">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                            <p>
                                                The generated Lorem Ipsum is therefore always free
                                                from repetition injected humour, or non-characteristic
                                                words etc. Susp endisse ultricies nisi vel quam
                                                suscipit
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <img src="{{ asset('img/avatar.jpg') }}" class="img-fluid rounded-circle p-3"
                                            style="width: 100px; height: 100px" alt="" />
                                        <div class="">
                                            <p class="mb-2" style="font-size: 14px">
                                                April 12, 2024
                                            </p>
                                            <div class="d-flex justify-content-between">
                                                <h5>Sam Peters</h5>
                                                <div class="d-flex mb-3">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                            <p class="text-dark">
                                                The generated Lorem Ipsum is therefore always free
                                                from repetition injected humour, or non-characteristic
                                                words etc. Susp endisse ultricies nisi vel quam
                                                suscipit
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="nav-vision" role="tabpanel">
                                    <p class="text-dark">
                                        Tempor erat elitr rebum at clita. Diam dolor diam ipsum et
                                        tempor sit. Aliqu diam amet diam et eos labore. 3
                                    </p>
                                    <p class="mb-0">
                                        Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam
                                        et eos labore. Clita erat ipsum et lorem et sit
                                    </p>
                                </div>
                            </div>
                        </div>
                        <form action="#">
                            <h4 class="mb-5 fw-bold">Leave a Reply</h4>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="text" class="form-control border-0 me-4"
                                            placeholder="Yur Name *" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="email" class="form-control border-0" placeholder="Your Email *" />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="border-bottom rounded my-4">
                                        <textarea name="" id="" class="form-control border-0" cols="30" rows="8"
                                            placeholder="Your Review *" spellcheck="false"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between py-3 mb-5">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-3">Please rate:</p>
                                            <div class="d-flex align-items-center" style="font-size: 12px">
                                                <i class="fa fa-star text-muted"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        </div>
                                        <a href="#"
                                            class="btn border border-secondary text-primary rounded-pill px-4 py-3">
                                            Post Comment</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-3">
                    <div class="row g-4 fruite">
                        <div class="col-lg-12">
                            <div class="input-group w-100 mx-auto d-flex mb-4">
                                <input type="search" class="form-control p-3" placeholder="keywords"
                                    aria-describedby="search-icon-1" />
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                            <div class="mb-4">
                                <h4>Categories</h4>
                                <ul class="list-unstyled fruite-categorie">
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="#"><i class="fas fa-apple-alt me-2"></i>Apples</a>
                                            <span>(3)</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="#"><i class="fas fa-apple-alt me-2"></i>Oranges</a>
                                            <span>(5)</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="#"><i class="fas fa-apple-alt me-2"></i>Strawbery</a>
                                            <span>(2)</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="#"><i class="fas fa-apple-alt me-2"></i>Banana</a>
                                            <span>(8)</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="#"><i class="fas fa-apple-alt me-2"></i>Pumpkin</a>
                                            <span>(5)</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h4 class="mb-4">Featured products</h4>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/featur-1.jpg') }}" class="img-fluid rounded" alt="Image" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/featur-2.jpg') }}" class="img-fluid rounded" alt="" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/featur-3.jpg') }}" class="img-fluid rounded" alt="" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded me-4" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/vegetable-item-4.jpg') }}" class="img-fluid rounded" alt="" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded me-4" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/vegetable-item-5.jpg') }}" class="img-fluid rounded" alt="" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="rounded me-4" style="width: 100px; height: 100px">
                                    <img src="{{ asset('img/vegetable-item-6.jpg') }}" class="img-fluid rounded" alt="" />
                                </div>
                                <div>
                                    <h6 class="mb-2">Big Banana</h6>
                                    <div class="d-flex mb-2">
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star text-secondary"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="d-flex mb-2">
                                        <h5 class="fw-bold me-2">2.99 $</h5>
                                        <h5 class="text-danger text-decoration-line-through">
                                            4.11 $
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center my-4">
                                <a href="#"
                                    class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">Vew
                                    More</a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="position-relative">
                                <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid w-100 rounded" alt="" />
                                <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%)">
                                    <h3 class="text-secondary fw-bold">
                                        Fresh <br />
                                        Fruits <br />
                                        Banner
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Single Product End -->
    <!-- Popular Products Start -->
    <section class="container-fluid py-1 bazaar-section">
        <div class="container pb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
                <div>
                    <p class="text-secondary fw-bold text-uppercase mb-2">

                    </p>
                    <h2 class="display-6 mb-0">Related Products</h2>
                </div>
                <a href="{{ route('frontend.shop') }}" class="btn border border-secondary rounded-pill px-4 text-primary">Shop all
                    products <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="owl-carousel product-carousel" data-aos="fade-up" data-aos-delay="100">
                <div class="product-card">
                    <span class="product-badge product-badge-new">New</span>
                    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </button>
                    <div class="product-media">
                        <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Fresh Oranges">
                            <img src="{{ asset('img/fruite-item-1.jpg') }}" class="product-image product-image-primary"
                                alt="Fresh oranges" />
                            <img src="{{ asset('img/best-product-1.jpg') }}" class="product-image product-image-secondary"
                                alt="" aria-hidden="true" />
                        </a>
                        <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                aria-hidden="true"></i> Add to Cart</a>
                    </div>
                    <div class="product-body">
                        <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Fresh Oranges</a>
                        <div class="product-meta">
                            <div class="product-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="far fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="product-price-current">$4.99</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <span class="product-badge product-badge-sale">-15%</span>
                    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </button>
                    <div class="product-media">
                        <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Bell Peppers">
                            <img src="{{ asset('img/vegetable-item-4.jpg') }}" class="product-image product-image-primary"
                                alt="Fresh bell peppers" />
                            <img src="{{ asset('img/best-product-2.jpg') }}" class="product-image product-image-secondary"
                                alt="" aria-hidden="true" />
                        </a>
                        <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                aria-hidden="true"></i> Add to Cart</a>
                    </div>
                    <div class="product-body">
                        <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Bell Peppers</a>
                        <div class="product-meta">
                            <div class="product-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="far fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="product-price-old">$9.99</span>
                                <span class="product-price-current">$7.99</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <span class="product-badge product-badge-new">New</span>
                    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </button>
                    <div class="product-media">
                        <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Seedless Grapes">
                            <img src="{{ asset('img/fruite-item-5.jpg') }}" class="product-image product-image-primary"
                                alt="Fresh grapes" />
                            <img src="{{ asset('img/best-product-3.jpg') }}" class="product-image product-image-secondary"
                                alt="" aria-hidden="true" />
                        </a>
                        <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                aria-hidden="true"></i> Add to Cart</a>
                    </div>
                    <div class="product-body">
                        <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Seedless Grapes</a>
                        <div class="product-meta">
                            <div class="product-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="product-price-current">$5.49</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <button type="button" class="wishlist-button" aria-label="Add to wishlist">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </button>
                    <div class="product-media">
                        <a href="{{ route('frontend.product-detail') }}" class="product-image-link" aria-label="View Farm Potatoes">
                            <img src="{{ asset('img/vegetable-item-5.jpg') }}" class="product-image product-image-primary"
                                alt="Fresh potatoes" />
                            <img src="{{ asset('img/best-product-4.jpg') }}" class="product-image product-image-secondary"
                                alt="" aria-hidden="true" />
                        </a>
                        <a href="{{ route('frontend.cart') }}" class="product-cart-cta"><i class="fa fa-shopping-bag"
                                aria-hidden="true"></i> Add to Cart</a>
                    </div>
                    <div class="product-body">
                        <a href="{{ route('frontend.product-detail') }}" class="stretched-link product-title">Farm Potatoes</a>
                        <div class="product-meta">
                            <div class="product-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="far fa-star"></i>
                            </div>
                            <div class="product-price">
                                <span class="product-price-current">$3.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Popular Products End -->
@endsection
