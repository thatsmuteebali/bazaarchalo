@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Register</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Register</li>
        </ol>
    </div>

    <main class="container-fluid py-5 auth-page">
        <div class="container py-5">
            <div class="auth-card bg-light rounded p-4 p-md-5 mx-auto">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus text-secondary auth-icon"></i>
                    <h2 class="mb-2">Create an Account</h2>
                    <p class="mb-0 text-muted">
                        Join Fruitables for a fresher way to shop.
                    </p>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <sup>*</sup></label><input type="text"
                                class="form-control py-3" name="name" id="name" value="{{ old('name') }}" placeholder="Enter your name" required />
                            @error('name')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">username <sup>*</sup></label><input type="text"
                                class="form-control py-3" name="username" id="username" value="{{ old('username') }}" placeholder="Enter your username" required />
                            @error('username')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="registerEmail" class="form-label">Email <sup>*</sup></label><input type="email"
                                class="form-control py-3" name="email" value="{{ old('email') }}" id="registerEmail" placeholder="Enter your email" required />
                            @error('email')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="registerPhone" class="form-label">Phone Number <sup>*</sup></label><input
                                type="text" class="form-control py-3" value="{{ old('phone_number') }}" name="phone_number" id="registerPhone"
                                placeholder="Enter your phone number" required />
                            @error('phone_number')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="registerPassword" class="form-label">Password <sup>*</sup></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control py-3" id="registerPassword"
                                    placeholder="Create a password" required /><button
                                    class="btn border border-start-0 bg-white px-3" type="button"
                                    data-toggle-password="registerPassword" aria-label="Show password">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Confirm Password <sup>*</sup></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control py-3" id="confirmPassword"
                                    placeholder="confirm password" required /><button
                                    class="btn border border-start-0 bg-white px-3" type="button"
                                    data-toggle-confirm="confirmPassword" aria-label="Show password">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" name="term" type="checkbox" id="terms" required /><label
                                    class="form-check-label" for="terms">I agree to the
                                    <a href="{{ route('frontend.terms-and-conditions') }}" class="text-primary">Terms & Conditions</a>.</label>
                                @error('term')
                                    <p class="text-danger">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill text-white">
                                Create Account
                            </button>
                        </div>
                    </div>
                </form>
                <p id="registerMessage" class="text-center text-primary mt-4 mb-0" role="status" aria-live="polite"></p>
                <p class="text-center mt-4 mb-0">
                    <a href="{{ route('seller.register') }}" class="text-primary">Seller Register</a>
                </p>
                <p class="text-center mt-4 mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-primary">Login here</a>
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

        document
            .querySelectorAll("[data-toggle-confirm]")
            .forEach(function(button) {
                button.addEventListener("click", function() {
                    var input = document.getElementById(button.dataset.toggleConfirm);
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
