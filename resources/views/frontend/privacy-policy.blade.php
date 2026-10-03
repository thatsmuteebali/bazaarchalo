@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Privacy Policy</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Privacy Policy</li>
        </ol>
    </div>

    <main class="privacy-page">
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="privacy-simple bg-light rounded p-4 p-lg-5">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        Bazaar Chalo
                    </p>
                    <h2 class="display-6 mb-4">Privacy Policy</h2>
                    <div class="privacy-content">
                        <p><strong>Effective date: September 19, 2026</strong></p>
                        <p>
                            Bazaar Chalo respects your privacy. This Privacy Policy explains
                            what information we collect, how we use it, and the choices you
                            have when you use our website, marketplace, and services.
                        </p>

                        <h3>1. Information We Collect</h3>
                        <p>
                            We may collect information that you provide when you create an
                            account, place an order, contact us, or register as a seller.
                            This may include your name, email address, phone number,
                            delivery address, account details, order information, and seller
                            business details.
                        </p>
                        <p>
                            We may also collect basic technical information such as your
                            browser, device type, IP address, approximate location, and the
                            pages or features you use.
                        </p>

                        <h3>2. How We Use Your Information</h3>
                        <p>We use your information to:</p>
                        <ul>
                            <li>create and manage your account;</li>
                            <li>process orders, payments, deliveries, and returns;</li>
                            <li>connect customers with local sellers;</li>
                            <li>respond to questions and provide customer support;</li>
                            <li>send order updates and important service messages;</li>
                            <li>improve our website, products, and services; and</li>
                            <li>detect fraud, prevent misuse, and protect our users.</li>
                        </ul>

                        <h3>3. Sharing Your Information</h3>
                        <p>
                            We do not sell your personal information. We may share
                            information when it is necessary to provide our services,
                            including with the seller fulfilling your order, delivery
                            partners, payment providers, hosting providers, customer-support
                            providers, or analytics services.
                        </p>
                        <p>
                            We may also disclose information when required by law, legal
                            process, or when necessary to protect the rights, safety, and
                            security of Bazaar Chalo, our users, or others.
                        </p>

                        <h3>4. Cookies</h3>
                        <p>
                            We may use cookies and similar technologies to keep you signed
                            in, remember your preferences, understand website performance,
                            and improve your experience. You can manage cookies through your
                            browser settings. Disabling essential cookies may affect some
                            account or checkout features.
                        </p>

                        <h3>5. Data Security</h3>
                        <p>
                            We use reasonable technical and organisational safeguards to
                            protect your information from unauthorised access, loss, misuse,
                            or disclosure. However, no online service can guarantee complete
                            security. Please use a strong password and contact us if you
                            believe your account has been compromised.
                        </p>

                        <h3>6. Data Retention</h3>
                        <p>
                            We retain personal information only for as long as needed to
                            provide our services, complete transactions, meet legal and
                            accounting obligations, resolve disputes, and enforce our
                            agreements. When information is no longer required, we will
                            delete or anonymise it where practical.
                        </p>

                        <h3>7. Your Choices</h3>
                        <p>
                            You can review or update your account information from your
                            profile. You may unsubscribe from promotional emails by using
                            the unsubscribe link in the message. You may also contact us to
                            request access to, correction of, or deletion of your personal
                            information, subject to applicable law and identity
                            verification.
                        </p>

                        <h3>8. Children&#39;s Privacy</h3>
                        <p>
                            Our services are intended for adults and are not directed to
                            children under the age required by applicable law. We do not
                            knowingly collect personal information from children.
                        </p>

                        <h3>9. Changes to This Policy</h3>
                        <p>
                            We may update this Privacy Policy from time to time. If we make
                            important changes, we will publish the updated policy on this
                            page and revise the effective date.
                        </p>

                        <h3>10. Contact Us</h3>
                        <p class="mb-0">
                            If you have questions about this Privacy Policy or how we handle
                            your information, please contact us through our
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
