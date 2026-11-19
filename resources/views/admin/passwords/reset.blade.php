@extends('auth.passwords.layout')

@section('title', 'Reset Admin Password')

@section('subtitle')
    Choose a strong password for your admin account.
@endsection

@section('form')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please check the details below and try again.</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        <div class="login-form-group">
            <label for="email" class="login-form-label">Email address</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                <input id="email" type="email" class="login-input @error('email') is-invalid @enderror"
                    value="{{ old('email', $email) }}" autocomplete="email" required readonly>
            </div>
            @error('email')
                <div class="form-feedback-custom invalid-custom" role="alert">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password" class="login-form-label">New password</label>
            <div class="login-input-group">
                <i class="bi bi-shield-lock input-icon" aria-hidden="true"></i>
                <input id="password" type="password" name="password"
                    class="login-input login-input-password @error('password') is-invalid @enderror"
                    placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required>
                <button type="button" class="password-toggle-btn" data-target="password"
                    aria-label="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
            @error('password')
                <div class="form-feedback-custom invalid-custom" role="alert">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password_confirmation" class="login-form-label">Confirm new password</label>
            <div class="login-input-group">
                <i class="bi bi-shield-lock input-icon" aria-hidden="true"></i>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="login-input login-input-password" placeholder="Re-enter your new password"
                    autocomplete="new-password" minlength="8" required>
                <button type="button" class="password-toggle-btn" data-target="password_confirmation"
                    aria-label="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <span>Update password</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection
