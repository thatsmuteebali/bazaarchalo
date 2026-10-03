@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Refund Policy</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Refund Policy</li>
        </ol>
    </div>

    <main class="privacy-page">
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="privacy-simple bg-light rounded p-4 p-lg-5">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Bazaar Chalo
                    </p>
                    <h2 class="display-6 mb-4">Refund Policy</h2>
                    <div class="privacy-content">
                        <p><strong>Effective date: September 19, 2026</strong></p>
                        <p>
                            We want you to feel confident when shopping with local sellers
                            through Bazaar Chalo. This Refund Policy explains when an order
                            may qualify for a refund, replacement, or other resolution.
                        </p>

                        <h3>1. When You May Request a Refund</h3>
                        <p>
                            You may contact us about a refund when an order is missing,
                            damaged, incorrect, materially different from its listing, or
                            cancelled after payment. Perishable products should be reported
                            as soon as possible after delivery.
                        </p>

                        <h3>2. How to Request a Refund</h3>
                        <p>
                            Contact us through the
                            <a href="{{ route('frontend.contact') }}">Contact page</a> with your order number,
                            a description of the issue, and photographs or other supporting
                            details when relevant. We may contact the seller or delivery
                            partner to review the request.
                        </p>

                        <h3>3. Review and Resolution</h3>
                        <p>
                            Each request is reviewed based on the order details, product
                            type, seller information, delivery status, and applicable law.
                            An approved resolution may be a full refund, partial refund,
                            replacement, store credit, or another solution agreed with you.
                        </p>

                        <h3>4. Cancellations</h3>
                        <p>
                            You may request cancellation before the seller begins preparing
                            your order. Cancellation after preparation or dispatch may not
                            be eligible for a full refund. Any applicable cancellation
                            charge will be shown or explained before the request is
                            completed.
                        </p>

                        <h3>5. Refund Timing</h3>
                        <p>
                            Once approved, refunds are sent to the original payment method
                            whenever possible. Your bank or payment provider may require
                            additional time to make the amount available. Processing times
                            can vary by provider.
                        </p>

                        <h3>6. Non-Refundable Situations</h3>
                        <p>
                            A refund may not be available when an issue results from an
                            incorrect address, unavailable recipient, unsafe delivery
                            instructions, misuse of a product, or a change of mind after a
                            correctly fulfilled order, subject to your legal rights.
                        </p>

                        <h3>7. Seller Responsibility</h3>
                        <p>
                            Sellers are responsible for the quality, accuracy, packaging,
                            and lawful sale of their products. Bazaar Chalo may support
                            communication and resolution between customers and sellers, but
                            the final outcome may depend on the product and seller involved.
                        </p>

                        <h3>8. Contact Us</h3>
                        <p class="mb-0">
                            For questions about a refund or an existing order, please
                            contact us as soon as possible through our
                            <a href="{{ route('frontend.contact') }}">Contact page</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
