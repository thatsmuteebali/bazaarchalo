<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Screen - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template</title>

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

            <p class="login-subtitle">Please sign in to access your dashboard</p>

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- Email Input Group -->
                <div class="login-form-group">
                    <label for="email" class="login-form-label">Email Address</label>
                    <div class="login-input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="login-input" placeholder="name@company.com"
                            required>
                    </div>
                    @error('email')
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

        </div>
    </div>
    <!-- END: Authentication Container -->

    <!-- Local Bootstrap bundle -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Authentication interactions script -->
    <script src="../assets/js/auth.js"></script>
</body>

</html>
