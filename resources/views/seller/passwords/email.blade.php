@extends('auth.passwords.layout')

@section('title', 'Seller Password Recovery')

@section('subtitle')
    Enter the email address for your seller account to receive a secure password reset link.
@endsection

@section('form')
    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('seller.password.email') }}">
        @csrf

        <div class="login-form-group">
            <label for="email" class="login-form-label">Email address</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                <input id="email" type="email" name="email"
                    class="login-input @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="name@company.com"
                    autocomplete="email" maxlength="255" required autofocus>
            </div>
            @error('email')
                <div class="form-feedback-custom invalid-custom" role="alert">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-login">
            <span>Send reset link</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection
