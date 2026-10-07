<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template</title>

    <!-- SEO Optimization -->
    <meta name="description" content="Login Screen - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template">
    <meta name="author" content="Bazaar Chalo Team">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/favicon.ico">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">

    <!-- Main Design System & Custom Stylesheet -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .btn-social:hover{
            color: black;
        }
    </style>
</head>

<body>

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
            <a href="dashboard.html" class="login-brand text-decoration-none">
                <i class="bi bi-asterisk"></i>
                <span>Bazaar Chalo</span>
            </a>
            @include('dashboard-message')
            <p class="login-subtitle">Please sign in to access your dashboard</p>

            <!-- Login Form -->
            <form method="POST" action="{{ route('seller.login') }}">
                @csrf

                <!-- login Input Group -->
                <div class="login-form-group">
                    <label for="login" class="login-form-label">Email or Username</label>
                    <div class="login-input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="login" name="login" id="login" class="login-input"
                            placeholder="name@company.com" required>
                    </div>
                    @error('login')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input Group -->
                <div class="login-form-group">
                    <label for="password" class="login-form-label">Password</label>

                    <div class="login-input-group">
                        <i class="bi bi-shield-lock input-icon"></i>

                        <input type="password" name="password" id="password" class="login-input login-input-password"
                            placeholder="••••••••" required>

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

                <!-- Options (Remember me & Forgot Password) -->
                <div class="login-options">
                    <label class="custom-control-label">
                        <input type="checkbox" class="custom-checkbox-input" id="rememberMe">
                        <span>Remember Me</span>
                    </label>
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login" id="btn-submit">
                    <span>Sign In</span>
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
            <a href="{{ route('seller.social.redirect', 'google') }}" class="btn-social w-100 mb-3" type="button" id="btn-google">
                <i class="bi bi-google text-danger"></i>
                <span>Google</span>
            </a>

            <a href="{{ route('seller.social.redirect', 'facebook') }}" class="btn-social w-100 mb-3" type="button" id="btn-facebook">
                <i class="bi bi-facebook text-primary"></i>
                <span>Facebook</span>
            </a>

            <!-- Footer Link -->
            <p class="login-footer-text">
                Don't have an account? <a href="{{ route('seller.register') }}" id="link-register">Register Now</a>
            </p>

        </div>
    </div>
    <!-- END: Authentication Container -->

    <!-- Local Bootstrap bundle -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Authentication interactions script -->
    <script src="../assets/js/auth.js"></script>
</body>

</html>
