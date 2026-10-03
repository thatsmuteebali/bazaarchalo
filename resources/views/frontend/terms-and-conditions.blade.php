@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Terms and Conditions</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Terms and Conditions</li>
        </ol>
    </div>

    <main class="privacy-page">
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="privacy-simple bg-light rounded p-4 p-lg-5">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Bazaar Chalo
                    </p>
                    <h2 class="display-6 mb-4">Terms and Conditions</h2>
                    <div class="privacy-content">
                        <p><strong>Effective date: September 19, 2026</strong></p>
                        <p>
                            Welcome to Bazaar Chalo. These Terms and Conditions explain the
                            rules for using our website, marketplace, and services. By
                            accessing Bazaar Chalo or placing an order, you agree to follow
                            these terms.
                        </p>

                        <h3>1. About Bazaar Chalo</h3>
                        <p>
                            Bazaar Chalo connects customers with local sellers. Sellers are
                            responsible for their products, prices, availability, order
                            preparation, and the accuracy of their shop information. Bazaar
                            Chalo provides the marketplace and related services.
                        </p>

                        <h3>2. Accounts</h3>
                        <p>
                            You may need an account to place orders or use certain features.
                            You are responsible for providing accurate information, keeping
                            your password confidential, and all activity that takes place
                            through your account. Please contact us promptly if you believe
                            your account is being used without permission.
                        </p>

                        <h3>3. Products and Prices</h3>
                        <p>
                            Product descriptions, images, prices, stock, and availability
                            are provided by sellers and may change without notice. We work
                            to keep information accurate, but we cannot guarantee that every
                            listing is complete, current, or free from errors.
                        </p>
                        <p>
                            Prices, taxes, delivery charges, discounts, and the final order
                            total will be shown during checkout. An order may be cancelled
                            or adjusted if there is an obvious pricing, stock, or listing
                            error.
                        </p>

                        <h3>4. Orders and Payments</h3>
                        <p>
                            Submitting an order is a request to purchase. An order is
                            accepted when Bazaar Chalo or the relevant seller confirms it.
                            We may refuse, cancel, or limit an order when products are
                            unavailable, payment cannot be authorised, fraud is suspected,
                            or an operational issue occurs.
                        </p>
                        <p>
                            You agree to provide valid payment information and authorise the
                            applicable charges for accepted orders, delivery, taxes, and
                            other amounts shown at checkout.
                        </p>

                        <h3>5. Delivery</h3>
                        <p>
                            Delivery times are estimates and may be affected by seller
                            preparation, traffic, weather, location, or other circumstances.
                            You are responsible for providing a correct delivery address and
                            being available to receive the order. Delivery instructions
                            should be clear, safe, and lawful.
                        </p>

                        <h3>6. Returns, Refunds, and Cancellations</h3>
                        <p>
                            Returns, refunds, replacements, and cancellations may depend on
                            the product, seller, order status, and applicable law. Contact
                            us as soon as possible if an order is missing, damaged,
                            incorrect, or materially different from its listing. We may
                            request photographs or other details to review a claim.
                        </p>

                        <h3>7. Seller Responsibilities</h3>
                        <p>
                            Sellers must provide lawful, safe, and accurately described
                            products. Sellers are responsible for complying with applicable
                            business, tax, consumer-protection, food-safety, licensing, and
                            delivery requirements. Sellers must not upload misleading
                            content, infringing material, or products that are prohibited by
                            law or Bazaar Chalo rules.
                        </p>

                        <h3>8. Acceptable Use</h3>
                        <p>
                            You must not misuse the website, interfere with its operation,
                            attempt unauthorised access, submit false information, scrape
                            content without permission, abuse promotions, or use the service
                            for unlawful, fraudulent, harmful, or abusive activity.
                        </p>

                        <h3>9. Intellectual Property</h3>
                        <p>
                            Bazaar Chalo and its licensors own the website design, branding,
                            software, text, graphics, and other platform content unless
                            stated otherwise. You may use the service for personal or
                            authorised business purposes, but you may not copy, modify,
                            distribute, sell, or exploit platform content without written
                            permission.
                        </p>

                        <h3>10. Disclaimers and Liability</h3>
                        <p>
                            The website and services are provided on an available basis. To
                            the extent permitted by law, Bazaar Chalo is not responsible for
                            seller content, product quality, seller conduct, delivery delays
                            outside our reasonable control, or indirect losses arising from
                            your use of the marketplace.
                        </p>
                        <p>
                            Nothing in these terms excludes or limits a right or liability
                            that cannot legally be excluded or limited.
                        </p>

                        <h3>11. Suspension and Termination</h3>
                        <p>
                            We may suspend or close an account, remove content, cancel
                            orders, or restrict access when these terms are violated, when
                            required for security, or when necessary to protect Bazaar Chalo
                            and its users. You may stop using the service at any time.
                        </p>

                        <h3>12. Changes to These Terms</h3>
                        <p>
                            We may update these Terms and Conditions when our services,
                            requirements, or legal obligations change. We will publish the
                            updated version on this page and revise the effective date.
                            Continued use of Bazaar Chalo after an update means you accept
                            the revised terms.
                        </p>

                        <h3>13. Contact Us</h3>
                        <p class="mb-0">
                            If you have questions about these Terms and Conditions, please
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
