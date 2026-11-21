@extends('layouts.app')

@section('content')
    <div class="container py-5" style="margin-top: 150px">
        <div class="auth-card bg-light rounded p-4 p-md-5 mx-auto">
            <h1 class="h3 mb-3">Verify your email address</h1>
            <p class="text-muted">
                Before continuing, please verify your email address using the link we sent to
                <strong>{{ auth()->user()->email }}</strong>.
            </p>

            @if (session('status'))
                <p class="text-success" role="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary py-3 rounded-pill text-white w-100">
                    Resend verification email
                </button>
            </form>

            <p class="text-center mt-4 mb-0">
                <a href="#" class="text-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Back to Home</a>
            </p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
@endsection
