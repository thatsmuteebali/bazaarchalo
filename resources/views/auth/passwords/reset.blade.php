@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Reset Customer Password</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Reset Customer Password</li>
        </ol>
    </div>

    <main class="container-fluid py-5 auth-page">
        <div class="container py-5">
            <div class="auth-card bg-light rounded p-4 p-md-5 mx-auto">
                <div class="text-center mb-4">
                    <i class="bi bi-key-fill text-secondary auth-icon"></i>
                    <h2 class="mb-2">Create a new password</h2>
                    <p class="mb-0 text-muted">Choose a strong password you haven't used on your customer account before.
                    </p>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">Please check the details below and try again.</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ old('email', $email) }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" type="email" class="form-control py-3 @error('email') is-invalid @enderror"
                            value="{{ old('email', $email) }}" autocomplete="email" required readonly>
                        @error('email')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
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
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password <sup>*</sup></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control py-3"
                                id="confirmPassword" placeholder="confirm password" required /><button
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

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill text-white">
                        Update password
                    </button>
                </form>
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
