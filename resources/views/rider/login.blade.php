<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Login - Bazaar Chalo</title>
    <meta name="description" content="Sign in to your Bazaar Chalo rider account">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
</head>

<body>
    @php
        $webSetting = getWebsiteContent('setting');
    @endphp
    <div class="login-wrapper">
        <div class="login-bg-shape login-bg-shape-1"></div>
        <div class="login-bg-shape login-bg-shape-2"></div>

        <div class="login-card">
            <a href="{{ route('rider.login') }}" class="login-brand text-decoration-none">
                <img src="{{ asset('storage/'.ltrim($webSetting['Website Header Logo'], '/')) }}"
                    class="site-logo site-logo-header" alt="Bazaar Chalo" width="50" height="50">
                <span>Bazaar Chalo</span>
            </a>

            @include('dashboard-message')
            <p class="login-subtitle">Sign in to manage your deliveries</p>

            <form method="POST" action="{{ route('rider.login') }}">
                @csrf

                <div class="login-form-group">
                    <label for="login" class="login-form-label">Email, Username or Phone Number</label>
                    <div class="login-input-group">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="login" id="login"
                            class="login-input @error('login') is-invalid-custom @enderror"
                            value="{{ old('login') }}" placeholder="Email, username or 03000000000"
                            autocomplete="username" required autofocus>
                    </div>
                    @error('login')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="login-form-group">
                    <label for="password" class="login-form-label">Password</label>
                    <div class="login-input-group">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                            class="login-input login-input-password @error('password') is-invalid-custom @enderror"
                            placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="password-toggle-btn" data-target="password"
                            aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                @if (config('services.recaptcha.enabled'))
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    @error('g-recaptcha-response')
                        <div class="form-feedback-custom invalid-custom mb-3">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                @endif

                <button type="submit" class="btn-login" id="btn-submit">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p class="login-footer-text mt-3">Rider access is managed by your administrator.</p>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>

    @if (config('services.recaptcha.enabled'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form[action="{{ route('rider.login') }}"]');
                const button = document.getElementById('btn-submit');
                if (!form) return;

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (button) button.disabled = true;

                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'rider_login' })
                            .then(function (token) {
                                document.getElementById('g-recaptcha-response').value = token;
                                form.submit();
                            })
                            .catch(function () {
                                if (button) button.disabled = false;
                                alert('reCAPTCHA failed to load. Please refresh and try again.');
                            });
                    });
                });
            });
        </script>
    @endif
</body>

</html>
