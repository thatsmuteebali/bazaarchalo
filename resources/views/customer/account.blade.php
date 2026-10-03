@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">My Account</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">My Account</li>
        </ol>
    </div>

    <!-- Account Dashboard Start -->
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')

                <div class="col-lg-8 col-xl-9">
                    <div class="tab-content" id="account-tabContent">
                        <section class="tab-pane fade show active" id="account-overview" role="tabpanel"
                            aria-labelledby="account-overview-tab">
                            <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                                <div>
                                    <p class="text-secondary fw-bold text-uppercase mb-2">Welcome back</p>
                                    <h2 class="display-6 mb-0">Your account</h2>
                                </div><a href="{{ route('frontend.shop') }}" class="btn btn-primary rounded-pill py-2 px-4 text-white">Shop
                                    fresh products <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="account-stat bg-light rounded p-4"><i
                                            class="fas fa-shopping-bag"></i><span>Orders
                                            placed</span><strong>12</strong></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="account-stat bg-light rounded p-4"><i
                                            class="fas fa-heart"></i><span>Wishlist items</span><strong>4</strong></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="account-stat bg-light rounded p-4"><i class="fas fa-star"></i><span>Reward
                                            points</span><strong>260</strong></div>
                                </div>
                            </div>
                            <div class="bg-light rounded p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="mb-0">Recent order</h4><a class="account-inline-tab text-primary"
                                        href="{{ route('customer.orders') }}">View all</a>
                                </div>
                                <div class="account-order-row">
                                    <div><strong>#BC-10482</strong><small class="d-block text-muted">Green Basket ·
                                            Today</small></div><span
                                        class="badge bg-secondary text-dark rounded-pill px-3 py-2">Out for
                                        delivery</span><strong class="text-primary">$414.00</strong>
                                </div>
                            </div>
                        </section>

                        <section class="tab-pane fade" id="account-profile" role="tabpanel"
                            aria-labelledby="account-profile-tab">
                            <div class="account-panel bg-light rounded p-4 p-md-5">
                                <div class="mb-4">
                                    <p class="text-secondary fw-bold text-uppercase mb-2">Personal details</p>
                                    <h2 class="display-6 mb-2">Profile</h2>
                                    <p class="text-muted mb-0">Keep your personal information up to date.</p>
                                </div>
                                <form>
                                    <div class="row g-4">
                                        <div class="col-md-6"><label class="form-label">First name</label><input
                                                type="text" class="form-control" value="Sarah"></div>
                                        <div class="col-md-6"><label class="form-label">Last name</label><input
                                                type="text" class="form-control" value="Johnson"></div>
                                        <div class="col-md-6"><label class="form-label">Email address</label><input
                                                type="email" class="form-control" value="sarah.johnson@example.com">
                                        </div>
                                        <div class="col-md-6"><label class="form-label">Phone number</label><input
                                                type="tel" class="form-control" value="+1 202 555 0148"></div>
                                        <div class="col-12"><label class="form-label">About you</label>
                                            <textarea class="form-control" rows="4" placeholder="Tell us a little about yourself"></textarea>
                                        </div>
                                        <div class="col-12"><button type="submit"
                                                class="btn btn-primary rounded-pill py-3 px-5 text-white">Save
                                                changes</button></div>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <section class="tab-pane fade" id="account-orders" role="tabpanel"
                            aria-labelledby="account-orders-tab">
                            <div class="account-panel bg-light rounded p-4 p-md-5">
                                <div class="mb-4">
                                    <p class="text-secondary fw-bold text-uppercase mb-2">Your shopping history</p>
                                    <h2 class="display-6 mb-2">My Orders</h2>
                                    <p class="text-muted mb-0">View and track all your Bazaar Chalo orders.</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table account-orders-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>#BC-10482</strong><small class="d-block text-muted">3
                                                        items</small></td>
                                                <td>Today</td>
                                                <td><span class="badge bg-secondary text-dark rounded-pill px-3 py-2">Out
                                                        for delivery</span></td>
                                                <td><strong>$414.00</strong></td>
                                                <td><a href="{{ route('customer.order-detail') }}"
                                                        class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">Track</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>#BC-10371</strong><small class="d-block text-muted">5
                                                        items</small></td>
                                                <td>18 Aug 2026</td>
                                                <td><span class="badge bg-primary rounded-pill px-3 py-2">Delivered</span>
                                                </td>
                                                <td><strong>$86.50</strong></td>
                                                <td><a href="{{ route('customer.order-detail') }}"
                                                        class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>#BC-10192</strong><small class="d-block text-muted">2
                                                        items</small></td>
                                                <td>04 Aug 2026</td>
                                                <td><span class="badge bg-primary rounded-pill px-3 py-2">Delivered</span>
                                                </td>
                                                <td><strong>$42.75</strong></td>
                                                <td><a href="{{ route('customer.order-detail') }}"
                                                        class="btn btn-sm border border-secondary rounded-pill px-3 text-primary">View</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <section class="tab-pane fade" id="account-wishlist" role="tabpanel"
                            aria-labelledby="account-wishlist-tab">
                            <div class="mb-4">
                                <p class="text-secondary fw-bold text-uppercase mb-2">Saved for later</p>
                                <h2 class="display-6 mb-2">Wishlist</h2>
                                <p class="text-muted mb-0">Your favourite local products, ready when you are.</p>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="wishlist-item bg-light rounded p-3 d-flex align-items-center"><img
                                            src="{{ asset('img/fruite-item-1.jpg') }}" alt="Fresh oranges">
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="mb-1">Fresh Oranges</h5><small class="text-muted">Juicy and
                                                naturally sweet</small><strong class="d-block mt-2 text-primary">$4.99 /
                                                kg</strong>
                                        </div><a href="{{ route('frontend.cart') }}"
                                            class="btn btn-sm border border-secondary rounded-circle btn-sm-square text-primary"
                                            aria-label="Add oranges to cart"><i class="fas fa-shopping-bag"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wishlist-item bg-light rounded p-3 d-flex align-items-center"><img
                                            src="{{ asset('img/vegetable-item-4.jpg') }}" alt="Bell peppers">
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="mb-1">Bell Peppers</h5><small class="text-muted">Crisp, colourful
                                                and local</small><strong class="d-block mt-2 text-primary">$7.99 /
                                                kg</strong>
                                        </div><a href="{{ route('frontend.cart') }}"
                                            class="btn btn-sm border border-secondary rounded-circle btn-sm-square text-primary"
                                            aria-label="Add peppers to cart"><i class="fas fa-shopping-bag"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wishlist-item bg-light rounded p-3 d-flex align-items-center"><img
                                            src="{{ asset('img/fruite-item-5.jpg') }}" alt="Seedless grapes">
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="mb-1">Seedless Grapes</h5><small class="text-muted">A fresh snack
                                                for every day</small><strong class="d-block mt-2 text-primary">$5.49 /
                                                kg</strong>
                                        </div><a href="{{ route('frontend.cart') }}"
                                            class="btn btn-sm border border-secondary rounded-circle btn-sm-square text-primary"
                                            aria-label="Add grapes to cart"><i class="fas fa-shopping-bag"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="wishlist-item bg-light rounded p-3 d-flex align-items-center"><img
                                            src="{{ asset('img/vegetable-item-5.jpg') }}" alt="Farm potatoes">
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="mb-1">Farm Potatoes</h5><small class="text-muted">Perfect for
                                                family meals</small><strong class="d-block mt-2 text-primary">$3.99 /
                                                kg</strong>
                                        </div><a href="{{ route('frontend.cart') }}"
                                            class="btn btn-sm border border-secondary rounded-circle btn-sm-square text-primary"
                                            aria-label="Add potatoes to cart"><i class="fas fa-shopping-bag"></i></a>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="tab-pane fade" id="account-addresses" role="tabpanel"
                            aria-labelledby="account-addresses-tab">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <div>
                                    <p class="text-secondary fw-bold text-uppercase mb-2">Where we deliver</p>
                                    <h2 class="display-6 mb-2">Addresses</h2>
                                    <p class="text-muted mb-0">Manage your saved delivery locations.</p>
                                </div><button type="button" class="btn btn-primary rounded-pill py-2 px-4 text-white"><i
                                        class="fas fa-plus me-2"></i>Add address</button>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="address-card bg-light rounded p-4 h-100">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h5 class="mb-0">Home <span
                                                    class="badge bg-secondary text-dark ms-2">Default</span></h5><button
                                                type="button" class="btn btn-sm text-primary"
                                                aria-label="Edit home address"><i class="fas fa-pen"></i></button>
                                        </div>
                                        <p class="mb-1">Sarah Johnson</p>
                                        <p class="text-muted mb-0">1429 Netus Rd<br>New York, NY 48247<br>United States
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="address-card bg-light rounded p-4 h-100">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h5 class="mb-0">Work</h5><button type="button"
                                                class="btn btn-sm text-primary" aria-label="Edit work address"><i
                                                    class="fas fa-pen"></i></button>
                                        </div>
                                        <p class="mb-1">Sarah Johnson</p>
                                        <p class="text-muted mb-0">88 Market Street<br>New York, NY 10005<br>United
                                            States</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="tab-pane fade" id="account-settings" role="tabpanel"
                            aria-labelledby="account-settings-tab">
                            <div class="account-panel bg-light rounded p-4 p-md-5">
                                <div class="mb-4">
                                    <p class="text-secondary fw-bold text-uppercase mb-2">Account preferences</p>
                                    <h2 class="display-6 mb-2">Settings</h2>
                                    <p class="text-muted mb-0">Choose how Bazaar Chalo keeps you updated.</p>
                                </div>
                                <div class="account-setting-row">
                                    <div><strong>Order updates</strong><small class="d-block text-muted">Get delivery
                                            and order notifications.</small></div>
                                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                            checked aria-label="Order updates"></div>
                                </div>
                                <div class="account-setting-row">
                                    <div><strong>New offers and local shops</strong><small class="d-block text-muted">Hear
                                            about fresh products and nearby
                                            sellers.</small></div>
                                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                            checked aria-label="Offers and local shops"></div>
                                </div>
                                <div class="account-setting-row">
                                    <div><strong>Personalised recommendations</strong><small
                                            class="d-block text-muted">Receive suggestions based on your
                                            shopping.</small></div>
                                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                            aria-label="Personalised recommendations"></div>
                                </div><button type="button"
                                    class="btn btn-primary rounded-pill py-3 px-5 text-white mt-4">Save
                                    preferences</button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Account Dashboard End -->
@endsection

@section('customjs')
@endsection
