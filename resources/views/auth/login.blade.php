@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Login</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Login</li>
        </ol>
    </div>

    <main class="container-fluid py-5 auth-page">
        <div class="container py-5">
            <div class="auth-card bg-light rounded p-4 p-md-5 mx-auto">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle text-secondary auth-icon"></i>
                    <h2 class="mb-2">Welcome Back</h2>
                    <p class="mb-0 text-muted">Sign in to continue shopping fresh.</p>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-item mb-4">
                        <label for="login" class="form-label">Email or Username <sup class="text-danger">*</sup></label>
                        <input type="login" name="login" class="form-control py-3" id="login"
                            placeholder="Enter your login" required />
                        @error('login')
                            <p class="text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="form-item mb-3">
                        <label for="loginPassword" class="form-label">Password <sup class="text-danger">*</sup></label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control py-3" id="loginPassword"
                                placeholder="Enter your password" required />
                            <button class="btn border border-start-0 bg-white px-3" type="button"
                                data-toggle-password="loginPassword" aria-label="Show password">
                                <i class="fas fa-eye text-primary"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" /><label
                                class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="text-primary">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill text-white">
                        Sign In
                    </button>

                    <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger w-100  mt-2 py-3 rounded-pill">
                        <i class="fab fa-google"></i> Login with Google
                    </a>

                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-outline-danger w-100  mt-2 py-3 rounded-pill">
                        <i class="fab fa-facebook"></i> Login with Facebook
                    </a>

                </form>
                <p id="loginMessage" class="text-center text-primary mt-4 mb-0" role="status" aria-live="polite">
                </p>
                <p class="text-center mt-4 mb-0">
                    Customer account?
                    <a href="{{ route('register') }}" class="text-primary">Create an account</a>
                </p>
                <p class="text-center mt-3 mb-0">
                    Want to sell with us?
                    <a href="{{ route('seller.register') }}" class="text-primary">Register your shop</a>
                </p>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
    <script>
        document
            .querySelectorAll("[data-toggle-password]")
            .forEach(function(button) {
                button.addEventListener("click", function() {
                    var input = document.getElementById(button.dataset.togglePassword);
                    var visible = input.type === "text";
                    input.type = visible ? "password" : "text";
                    button.setAttribute(
                        "aria-label",
                        visible ? "Show password" : "Hide password",
                    );
                    button
                        .querySelector("i")
                        .classList.toggle("fa-eye-slash", !visible);
                    button.querySelector("i").classList.toggle("fa-eye", visible);
                });
            });
    </script>
@endsection
