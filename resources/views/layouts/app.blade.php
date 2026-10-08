<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Bazaar Chalo - Fresh Local Shopping</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet" />

    <!-- AOS (Animate On Scroll) Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    @yield('customCss')
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                        <a href="#" class="text-white">123 Street, New York</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#"
                            class="text-white">Email@Example.com</a></small>
                </div>
                <div class="top-link pe-2">
                    <a href="https://www.facebook.com/" class="text-white" target="_blank"><small
                            class="text-white mx-2">Facebook</small>/</a>
                    <a href="https://www.instagram.com/" class="text-white" target="_blank"><small
                            class="text-white mx-2">Instagram</small>/</a>
                    <a href="https://www.tiktok.com/" class="text-white" target="_blank"><small
                            class="text-white ms-2">Tiktok</small></a>
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="{{ route('frontend.home') }}" class="navbar-brand" aria-label="Bazaar Chalo home">
                    <img src="{{ asset('img/logo.png') }}" class="site-logo site-logo-header" alt="Bazaar Chalo">
                </a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="{{ route('frontend.home') }}" class="nav-item nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}">Home</a>

                        <div class="nav-item dropdown mega-menu-wrapper">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">Women</a>
                            <div class="dropdown-menu mega-menu p-4">
                                <div class="mega-menu-content">
                                    <div class="mega-menu-columns-wrap">
                                        <div class="mega-menu-columns">
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Clothing</p>
                                                <a href="{{ route('frontend.shop') }}">Dresses</a>
                                                <a href="{{ route('frontend.shop') }}">Tops & Blouses</a>
                                                <a href="{{ route('frontend.shop') }}">Jackets & Coats</a>
                                                <a href="{{ route('frontend.shop') }}">Bottoms</a>
                                            </div>
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Accessories</p>
                                                <a href="{{ route('frontend.shop') }}">Bags & Purses</a>
                                                <a href="{{ route('frontend.shop') }}">Scarves</a>
                                                <a href="{{ route('frontend.shop') }}">Jewellery</a>
                                                <a href="{{ route('frontend.shop') }}">Shoes</a>
                                            </div>
                                        </div>
                                        <a href="{{ route('frontend.shop') }}" class="mega-menu-viewall">View All Women <i
                                                class="fas fa-arrow-right"></i></a>
                                    </div>
                                    <a href="{{ route('frontend.shop') }}" class="mega-menu-feature">
                                        <img src="{{ asset('img/featur-2.jpg') }}" alt="Women's Collection">
                                        <span class="mega-menu-feature-badge">New Season 2026</span>
                                        <span>
                                            <strong>Women's Collection</strong>
                                            <small class="mega-menu-feature-cta">Shop Now <i
                                                    class="fas fa-arrow-right"></i></small>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="nav-item dropdown mega-menu-wrapper">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">Men</a>
                            <div class="dropdown-menu mega-menu p-4">
                                <div class="mega-menu-content">
                                    <div class="mega-menu-columns-wrap">
                                        <div class="mega-menu-columns">
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Clothing</p>
                                                <a href="{{ route('frontend.shop') }}">Shirts</a>
                                                <a href="{{ route('frontend.shop') }}">Jackets</a>
                                                <a href="{{ route('frontend.shop') }}">Pants & Chinos</a>
                                                <a href="{{ route('frontend.shop') }}">T-Shirts</a>
                                            </div>
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Footwear & More</p>
                                                <a href="{{ route('frontend.shop') }}">Shoes</a>
                                                <a href="{{ route('frontend.shop') }}">Bags</a>
                                                <a href="{{ route('frontend.shop') }}">Accessories</a>
                                                <a href="{{ route('frontend.offers') }}">Sale</a>
                                            </div>
                                        </div>
                                        <a href="{{ route('frontend.shop') }}" class="mega-menu-viewall">View All Men <i
                                                class="fas fa-arrow-right"></i></a>
                                    </div>
                                    <a href="{{ route('frontend.shop') }}" class="mega-menu-feature">
                                        <img src="{{ asset('img/hero-img-1.png') }}" alt="Men's Collection">
                                        <span class="mega-menu-feature-badge">Just Arrived</span>
                                        <span>
                                            <strong>Men's Collection</strong>
                                            <small class="mega-menu-feature-cta">Shop Now <i
                                                    class="fas fa-arrow-right"></i></small>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="nav-item dropdown mega-menu-wrapper">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">Kids</a>
                            <div class="dropdown-menu mega-menu p-4">
                                <div class="mega-menu-content">
                                    <div class="mega-menu-columns-wrap">
                                        <div class="mega-menu-columns">
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Girls</p>
                                                <a href="{{ route('frontend.shop') }}">Dresses</a>
                                                <a href="{{ route('frontend.shop') }}">Tops</a>
                                                <a href="{{ route('frontend.shop') }}">Bottoms</a>
                                                <a href="{{ route('frontend.shop') }}">Outerwear</a>
                                            </div>
                                            <div class="mega-menu-column">
                                                <p class="mega-menu-heading">Boys</p>
                                                <a href="{{ route('frontend.shop') }}">Shirts</a>
                                                <a href="{{ route('frontend.shop') }}">Pants</a>
                                                <a href="{{ route('frontend.shop') }}">T-Shirts</a>
                                                <a href="{{ route('frontend.shop') }}">Outerwear</a>
                                            </div>
                                        </div>
                                        <a href="{{ route('frontend.shop') }}" class="mega-menu-viewall">View All Kids <i
                                                class="fas fa-arrow-right"></i></a>
                                    </div>
                                    <a href="{{ route('frontend.shop') }}" class="mega-menu-feature">
                                        <img src="{{ asset('img/best-product-3.jpg') }}" alt="Kids' Styles">
                                        <span class="mega-menu-feature-badge">New Arrivals</span>
                                        <span>
                                            <strong>Kids' Styles</strong>
                                            <small class="mega-menu-feature-cta">Shop Now <i
                                                    class="fas fa-arrow-right"></i></small>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('frontend.shop') }}" class="nav-item nav-link {{ request()->routeIs('frontend.shop') ? 'active' : '' }}">Shop</a>
                        <a href="{{ route('frontend.about') }}" class="nav-item nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}">About</a>
                    </div>
                    <div class="d-flex align-items-center m-3 me-0">
                        <div class="nav-search-box" id="navSearchBox">
                            <i class="fas fa-search nav-search-icon"></i>
                            <input type="search" class="nav-search-input" id="navSearchInput"
                                placeholder="Search for a product..." aria-label="Search for a product" />
                        </div>
                        <button
                            class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4"
                            id="navSearchToggle" aria-expanded="false" aria-controls="navSearchBox"
                            aria-label="Search">
                            <i class="fas fa-search text-primary"></i>
                        </button>
                        <a href="{{ route('frontend.cart') }}" class="position-relative me-4 my-auto nav-cart-link">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            <span
                                class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1 nav-cart-count"
                                style="top: -5px; left: 15px; height: 20px; min-width: 20px">0</span>
                        </a>
                        <a href="{{ route('home') }}" class="my-auto" aria-label="Account">
                            <i class="fas fa-user fa-2x"></i>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    @yield('content')

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5" data-aos="fade-up">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5)">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between">
                    <div class="footer-brand">
                        <a href="{{ route('frontend.home') }}" aria-label="Bazaar Chalo home">
                            <img src="{{ asset('img/logo.png') }}" class="site-logo site-logo-footer" alt="Bazaar Chalo">
                        </a>
                        <p class="text-secondary mb-0">Shop local. Live fresh.</p>
                    </div>
                    <div class="">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Bazaar Chalo</h4>
                        <a class="btn-link" href="{{ route('frontend.about') }}">About Us</a>
                        <a class="btn-link" href="{{ route('frontend.contact') }}">Contact Us</a>
                        <a class="btn-link" href="#">Careers</a>
                        <a class="btn-link" href="#">Become a Seller</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Customer</h4>
                        <a class="btn-link" href="{{ route('login') }}">Login</a>
                        <a class="btn-link" href="{{ route('register') }}">Register</a>
                        <a class="btn-link" href="{{ route('customer.account') }}">My Account</a>
                        <a class="btn-link" href="{{ route('customer.orders') }}">My Orders</a>
                        <a class="btn-link" href="{{ route('customer.order-tracking') }}">Track Order</a>
                        <a class="btn-link" href="{{ route('frontend.faq') }}">FAQ /Help Center</a>
                        <a class="btn-link" href="">Return</a>
                        <a class="btn-link" href="{{ route('frontend.privacy-policy') }}">Privacy Policy</a>
                        <a class="btn-link" href="{{ route('frontend.terms-and-conditions') }}">Terms & Conditions</a>
                        <a class="btn-link" href="{{ route('frontend.refund-policy') }}">Refund Policy</a>
                        <a class="btn-link" href="{{ route('frontend.delivery-policy') }}">Delivery Policy</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Seller</h4>
                        <a class="btn-link" href="{{ route('seller.login') }}">Seller Login</a>
                        <a class="btn-link" href="{{ route('seller.register') }}">Seller Registration</a>
                        <a class="btn-link" href="{{ route('frontend.faq') }}">Seller Guide</a>
                        <a class="btn-link" href="{{ route('frontend.faq') }}">Seller Support</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Contact</h4>
                        <p>Address: 1429 Netus Rd, NY 48247</p>
                        <p>Email: Example@gmail.com</p>
                        <p>Phone: +0123 4567 8910</p>
                        <p>Payment Accepted</p>
                        <img src="{{ asset('img/payment.png') }}" class="img-fluid" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mb-3 mb-md-0">
                    <span class="text-light"><i class="fas fa-copyright text-light me-2"></i>2026: <a
                            href="#">Bazaar Chalo</a>, All rights reserved.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Cart Drawer Start -->
    <div class="cart-drawer-backdrop" id="cartDrawerBackdrop"></div>
    <div class="cart-drawer" id="cartDrawer" aria-hidden="true">
        <div class="cart-drawer-header">
            <h5><i class="fa fa-shopping-bag me-2"></i>Your Cart</h5>
            <button type="button" class="cart-drawer-close" id="cartDrawerClose" aria-label="Close cart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-drawer-items" id="cartDrawerItems">
            <div class="cart-drawer-empty">
                <i class="fas fa-shopping-bag"></i>
                <p>Your cart is empty</p>
            </div>
        </div>
        <div class="cart-drawer-footer" id="cartDrawerFooter" style="display: none">
            <div class="cart-drawer-subtotal">
                <span>Subtotal</span>
                <strong id="cartDrawerSubtotal">$0.00</strong>
            </div>
            <a href="{{ route('frontend.cart') }}" class="btn btn-outline-dark w-100 mb-2">
                <i class="fa fa-shopping-cart me-2"></i>View Cart
            </a>
            <a href="{{ route('customer.checkout') }}" class="btn btn-primary w-100 mb-2">
                <i class="fas fa-lock me-2"></i>Checkout
            </a>
            <button type="button" class="cart-drawer-continue" id="cartDrawerContinue">Continue Shopping</button>
        </div>
    </div>
    <!-- Cart Drawer End -->

    <!-- Back to Top Start -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>
    <!-- Back to Top End -->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- AOS (Animate On Scroll) Library -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>

    @yield('customjs')
</body>

</html>
