@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">
            Frequently Asked Questions
        </h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">FAQs</li>
        </ol>
    </div>

    <main class="container-fluid py-5 faq-page">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px">
                <p class="text-secondary fw-bold text-uppercase mb-2">
                    Need a little help?
                </p>
                <h2 class="display-6 mb-3">How can we help?</h2>
                <p class="mb-0">
                    Find quick answers about shopping fresh, supporting local sellers,
                    and receiving your order.
                </p>
            </div>
            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-lg-6">
                    <div class="faq-group bg-light rounded p-4 p-md-5 mb-4">
                        <div class="accordion" id="shoppingFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="shoppingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#shoppingAnswerOne" aria-expanded="true"
                                        aria-controls="shoppingAnswerOne">
                                        What is Bazaar Chalo?
                                    </button>
                                </h2>
                                <div id="shoppingAnswerOne" class="accordion-collapse collapse show"
                                    aria-labelledby="shoppingOne" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Bazaar Chalo is a local shopping marketplace that connects
                                        you with trusted neighbourhood sellers and fresh everyday
                                        products.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="shoppingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#shoppingAnswerTwo" aria-expanded="false"
                                        aria-controls="shoppingAnswerTwo">
                                        How do I find products near me?
                                    </button>
                                </h2>
                                <div id="shoppingAnswerTwo" class="accordion-collapse collapse"
                                    aria-labelledby="shoppingTwo" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Browse the Shop page or explore a local seller to discover
                                        products available in your area.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="shoppingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#shoppingAnswerThree" aria-expanded="false"
                                        aria-controls="shoppingAnswerThree">
                                        Can I add products from different shops?
                                    </button>
                                </h2>
                                <div id="shoppingAnswerThree" class="accordion-collapse collapse"
                                    aria-labelledby="shoppingThree" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Your basket is organised around local shops so each seller
                                        can prepare and fulfil your products with care.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="sellerOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#sellerAnswerOne" aria-expanded="false"
                                        aria-controls="sellerAnswerOne">
                                        How can I become a seller?
                                    </button>
                                </h2>
                                <div id="sellerAnswerOne" class="accordion-collapse collapse" aria-labelledby="sellerOne"
                                    data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Select Register from the navigation and create your shop profile. You can then
                                        list products and reach local customers.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="sellerTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#sellerAnswerTwo" aria-expanded="false"
                                        aria-controls="sellerAnswerTwo">
                                        What can I sell on Bazaar Chalo?
                                    </button>
                                </h2>
                                <div id="sellerAnswerTwo" class="accordion-collapse collapse" aria-labelledby="sellerTwo"
                                    data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Local shops can showcase fresh produce, pantry essentials, and other quality
                                        products for their community.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="deliveryOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#deliveryAnswerOne" aria-expanded="false"
                                        aria-controls="deliveryAnswerOne">
                                        How long does delivery take?
                                    </button>
                                </h2>
                                <div id="deliveryAnswerOne" class="accordion-collapse collapse"
                                    aria-labelledby="deliveryOne" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Delivery times depend on the shop and your location. Your checkout and order
                                        tracking pages show the latest estimated delivery window.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="deliveryTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#deliveryAnswerTwo" aria-expanded="false"
                                        aria-controls="deliveryAnswerTwo">
                                        Where can I track my order?
                                    </button>
                                </h2>
                                <div id="deliveryAnswerTwo" class="accordion-collapse collapse"
                                    aria-labelledby="deliveryTwo" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Open Order Tracking from the Pages menu or visit the tracking link in your order
                                        detail page.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="deliveryThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#deliveryAnswerThree" aria-expanded="false"
                                        aria-controls="deliveryAnswerThree">
                                        What if something is missing from my order?
                                    </button>
                                </h2>
                                <div id="deliveryAnswerThree" class="accordion-collapse collapse"
                                    aria-labelledby="deliveryThree" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Please contact us with your order number and we will help you resolve the issue
                                        with the local seller.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="accountOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accountAnswerOne" aria-expanded="false"
                                        aria-controls="accountAnswerOne">
                                        How do I update my account details?
                                    </button>
                                </h2>
                                <div id="accountAnswerOne" class="accordion-collapse collapse"
                                    aria-labelledby="accountOne" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Open My Account and choose Profile to update your name, email address, or phone
                                        number.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="accountTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accountAnswerTwo" aria-expanded="false"
                                        aria-controls="accountAnswerTwo">
                                        Which payment methods are accepted?
                                    </button>
                                </h2>
                                <div id="accountAnswerTwo" class="accordion-collapse collapse"
                                    aria-labelledby="accountTwo" data-bs-parent="#shoppingFaq">
                                    <div class="accordion-body">
                                        Available payment methods are shown during checkout and may vary by seller and
                                        delivery area.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="text-center bg-light rounded p-4 p-md-5 mt-5">
                <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-3">
                    <i class="fas fa-headset fa-2x text-white"></i>
                </div>
                <h3 class="mb-2">Still need help?</h3>
                <p class="mb-4">
                    Our team is happy to help with your order or local shopping
                    experience.
                </p>
                <a href="{{ route('frontend.contact') }}" class="btn btn-primary rounded-pill py-3 px-5 text-white">Contact us <i
                        class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
