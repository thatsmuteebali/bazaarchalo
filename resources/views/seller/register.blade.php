<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Register - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template</title>

    <!-- SEO Optimization -->
    <meta name="description" content="Login Screen - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template">
    <meta name="author" content="Bazaar Chalo Team">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">

    <!-- Main Design System & Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <style>
        .login-card {
            max-width: 750px !important;
        }
    </style>
</head>

<body>
    @php
        $webSetting = getWebsiteContent('setting');
    @endphp
    <!-- ==========================================
         START: Authentication Container & Login Card
         ========================================== -->
    <div class="login-wrapper">
        <!-- Glowing background shapes for modern visual appearance -->
        <div class="login-bg-shape login-bg-shape-1"></div>
        <div class="login-bg-shape login-bg-shape-2"></div>

        <!-- Main centered login card -->
        <div class="login-card">

            <!-- Brand Identity -->
            <a href="#" class="login-brand text-decoration-none">
                {{-- <i class="bi bi-asterisk"></i> --}}
                <img src="{{ asset('storage/' . ltrim($webSetting['Website Header Logo'], '/')) }}"
                    class="site-logo site-logo-header" alt="Bazaar Chalo" width="50" height="50">
                <span>Bazaar Chalo</span>
            </a>

            <p class="login-subtitle">Create your seller account to start selling with us</p>

            <!-- Login Form -->
            <form method="POST" action="{{ route('seller.register') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-6 login-form-group">
                        <label for="name" class="login-form-label">Full Name</label>
                        <div class="login-input-group">
                            <i class="bi bi-person  input-icon"></i>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="login-input" placeholder="seller"
                                required>
                        </div>
                        @error('name')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 login-form-group">
                        <label for="username" class="login-form-label">Username</label>
                        <div class="login-input-group">
                            <i class="bi bi-person-badge  input-icon"></i>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" class="login-input"
                                placeholder="seller123" required>
                        </div>
                        @error('username')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <!-- Email Input Group -->
                    <div class="col-md-6 login-form-group">
                        <label for="email" class="login-form-label">Email Address</label>
                        <div class="login-input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="login-input"
                                placeholder="name@company.com" required>
                        </div>
                        @error('email')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 login-form-group">
                        <label for="phone" class="login-form-label">Phone Number</label>
                        <div class="login-input-group">
                            <i class="bi bi-telephone  input-icon"></i>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="login-input"
                                placeholder="03000000000" required>
                        </div>
                        @error('phone')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input Group -->
                    <div class="col-md-6 login-form-group">
                        <label for="password" class="login-form-label">Password</label>

                        <div class="login-input-group">
                            <i class="bi bi-shield-lock input-icon"></i>

                            <input type="password" name="password" id="password"
                                class="login-input login-input-password" placeholder="••••••••" required>

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


                    <!-- Confirm Password Input Group -->
                    <div class="col-md-6 login-form-group">
                        <label for="password_confirmation" class="login-form-label">
                            Confirm Password
                        </label>

                        <div class="login-input-group">
                            <i class="bi bi-shield-lock input-icon"></i>

                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="login-input login-input-password" placeholder="••••••••" required>

                            <button type="button" class="password-toggle-btn" data-target="password_confirmation"
                                aria-label="Show password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- <hr> --}}
                    <h5 class="card-title mb-4">Shop Information</h5>
                    <div class="col-md-6 login-form-group">
                        <label for="shop_name" class="login-form-label">Shop Name</label>
                        <div class="login-input-group">
                            <i class="bi bi-shop  input-icon"></i>
                            <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name') }}" class="login-input"
                                placeholder="stylish garments" required>
                        </div>
                        @error('shop_name')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 login-form-group">
                        <label for="slug" class="login-form-label">Slug</label>
                        <div class="login-input-group">
                            <i class="bi bi-tag  input-icon"></i>
                            <input type="text" name="slug" id="slug" class="login-input"
                                value="{{ old('slug') }}" placeholder="eg: stylish-garments" required>
                        </div>
                        @error('slug')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="banner" class="form-label-custom">Shop Banner</label>

                        <input type="file" name="banner" id="banner" accept="image/*"
                            class="form-control-custom image @error('banner') is-invalid-custom @enderror">
                        <div class="mt-2">
                            <img src="{{ !empty($shop?->banner) ? asset('storage/' . $shop->banner) : asset('assets/images/no_image.png') }}"
                                alt="{{ !empty($shop?->banner) ? $shop->name : 'shop image' }}" class="image-preview"
                                style="width: 180px; height: 90px; object-fit: contain;">
                        </div>
                        {{-- <small class="text-muted">Optional. Maximum file size: 4 MB.</small> --}}
                        @error('banner')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label-custom">Address</label>
                        <textarea name="address" id="address" rows="4" maxlength="5000"
                            class="form-control-custom @error('address') is-invalid-custom @enderror" placeholder="Enter shop address">{{ old('address', $shop->address ?? '') }}</textarea>
                        @error('address')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label-custom">Description</label>
                        <textarea name="description" id="description" rows="4" maxlength="5000"
                            class="form-control-custom @error('description') is-invalid-custom @enderror" placeholder="Describe your shop">{{ old('description', $shop->description ?? '') }}</textarea>
                        @error('description')
                            <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>



                <!-- Options (Remember me & Forgot Password) -->
                <div class="login-options">
                    <label class="custom-control-label">
                        <input type="checkbox" class="custom-checkbox-input" id="rememberMe">
                        <span>Remember Me</span>
                    </label>
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>

                @if (config('services.recaptcha.enabled'))
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                    @error('g-recaptcha-response')
                        <div class="form-feedback-custom invalid-custom mb-3">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                @endif
                <!-- Submit Button -->
                <button type="submit" class="btn-login" id="btn-submit">
                    <span>Register</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <!-- Divider -->
            <div class="login-divider">Or sign in with</div>

            <!-- Social Logins -->
            {{-- <div class="social-login-grid">
                <button class="btn-social" type="button" id="btn-google">
                    <i class="bi bi-google text-danger"></i>
                    <span>Google</span>
                </button>
                <button class="btn-social" type="button" id="btn-github">
                    <i class="bi bi-github"></i>
                    <span>GitHub</span>
                </button>
            </div> --}}
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('seller.social.redirect', 'google') }}" class="btn-social w-100 mb-3"
                        type="button" id="btn-google">
                        <i class="bi bi-google text-danger"></i>
                        <span>Google</span>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="{{ route('seller.social.redirect', 'facebook') }}" class="btn-social w-100 mb-3"
                        type="button" id="btn-facebook">
                        <i class="bi bi-facebook text-primary"></i>
                        <span>Facebook</span>
                    </a>
                </div>
            </div>

            <!-- Footer Link -->
            <p class="login-footer-text">
                Have an account? <a href="{{ route('seller.login') }}" id="link-register">Login Now</a>
            </p>

        </div>
    </div>
    <!-- END: Authentication Container -->

    <!-- Local Bootstrap bundle -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Custom Authentication interactions script -->
    <script src="{{ asset('assets/js/auth.js') }}"></script>

    @if (config('services.recaptcha.enabled'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form[action="{{ route('seller.register') }}"]');
                const btn = document.getElementById('btn-submit');
                if (!form) return;

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (btn) btn.disabled = true;

                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                                action: 'seller_register'
                            })
                            .then(function(token) {
                                document.getElementById('g-recaptcha-response').value = token;
                                form.submit();
                            })
                            .catch(function() {
                                if (btn) btn.disabled = false;
                                alert('reCAPTCHA failed to load. Please refresh and try again.');
                            });
                    });
                });
            });
        </script>
    @endif
    <script>
        $(document).on("change", ".image", function(e) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $(".image-preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(e.target.files[0]);
        });
    </script>
</body>

</html>
