@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Forgot Password</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Forgot Password</li>
        </ol>
    </div>

    <main class="container-fluid py-5 auth-page">
        <div class="container py-5">
            <div class="auth-card bg-light rounded p-4 p-md-5 mx-auto">
                <div class="text-center mb-4">
                    <i class="bi bi-key-fill text-secondary auth-icon"></i>
                    <h2 class="mb-2">Forgot Password</h2>
                    <p class="mb-0 text-muted">Enter the email address linked to your customer account and we'll send you a secure reset link.</p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" type="email" name="email"
                            class="form-control py-3 @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="you@example.com" autocomplete="email" maxlength="255" required autofocus>
                        @error('email')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill text-white">
                        Send password reset link
                    </button>
                </form>

                <p class="text-center mt-4 mb-0">
                    Remember your password? <a href="{{ route('login') }}" class="text-primary">Back to customer login</a>
                </p>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
    
@endsection
