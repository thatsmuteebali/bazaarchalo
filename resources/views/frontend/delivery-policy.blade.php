@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Delivery Policy</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Delivery Policy</li>
        </ol>
    </div>

    <main class="privacy-page">
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="privacy-simple bg-light rounded p-4 p-lg-5">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Bazaar Chalo
                    </p>
                    <h2 class="display-6 mb-4">Delivery Policy</h2>
                    <div class="privacy-content">
                        <p><strong>Effective date: September 19, 2026</strong></p>
                        <p>
                            Bazaar Chalo helps local sellers deliver fresh products and
                            everyday essentials to customers. This Delivery Policy explains
                            how delivery works, what customers should expect, and how to
                            report a delivery issue.
                        </p>

                        <h3>1. Delivery Areas</h3>
                        <p>
                            Delivery availability depends on the seller, product, delivery
                            partner, and your address. Available delivery options and
                            charges are shown during checkout. Some products or sellers may
                            be available only within selected local areas.
                        </p>

                        <h3>2. Delivery Times</h3>
                        <p>
                            Estimated delivery times are shown during checkout or in your
                            order updates. Times are estimates and may change because of
                            seller preparation, traffic, weather, demand, public holidays,
                            or other circumstances outside reasonable control.
                        </p>

                        <h3>3. Delivery Charges</h3>
                        <p>
                            Delivery charges may vary by seller, distance, basket size,
                            delivery speed, and current offers. The applicable charge will
                            be displayed before you confirm your order. Promotional
                            free-delivery offers may have minimum order values or other
                            conditions.
                        </p>

                        <h3>4. Customer Responsibilities</h3>
                        <p>
                            Please provide a complete and accurate delivery address, phone
                            number, and delivery instructions. You should be available to
                            receive the order or provide a safe place where the delivery
                            partner may leave it. Additional charges or delays may apply
                            when an address is incorrect or the recipient is unavailable.
                        </p>

                        <h3>5. Order Preparation</h3>
                        <p>
                            Sellers are responsible for preparing, packing, and handing over
                            products for delivery. Fresh and perishable products may be
                            packed according to their type and expected delivery conditions.
                            Product availability may change before preparation begins.
                        </p>

                        <h3>6. Delayed or Failed Deliveries</h3>
                        <p>
                            Contact us through the
                            <a href="{{ route('frontend.contact') }}">Contact page</a> if your order is
                            significantly delayed, cannot be delivered, or shows an
                            incorrect delivery status. We will review the order with the
                            seller or delivery partner and help identify an appropriate
                            resolution.
                        </p>

                        <h3>7. Damaged, Missing, or Incorrect Items</h3>
                        <p>
                            Check your order when it arrives and report missing, damaged, or
                            incorrect items as soon as possible. Include your order number
                            and photographs when relevant. Refunds, replacements, or other
                            resolutions are handled under our
                            <a href="{{ route('frontend.refund-policy') }}">Refund Policy</a>.
                        </p>

                        <h3>8. Delivery Restrictions</h3>
                        <p>
                            We may be unable to deliver to restricted, unsafe, inaccessible,
                            or legally prohibited locations. Delivery partners may refuse a
                            delivery when conditions present a safety risk or when identity,
                            age, or other legal checks cannot be completed.
                        </p>

                        <h3>9. Contact Us</h3>
                        <p class="mb-0">
                            For delivery questions or help with an existing order, please
                            contact us through our <a href="{{ route('frontend.contact') }}">Contact page</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
