@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Profile</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.account') }}">My Account</a></li>
            <li class="breadcrumb-item active text-white">Profile</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')
                <section class="col-lg-8 col-xl-9">
                    <div class="account-panel bg-light rounded p-4 p-md-5">
                        <div class="mb-4">
                            <p class="text-secondary fw-bold text-uppercase mb-2">
                                Personal details
                            </p>
                            <h2 class="display-6 mb-2">Profile</h2>
                            <p class="text-muted mb-0">
                                Keep your personal information up to date.
                            </p>
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="status">{{ session('status') }}</div>
                        @endif
                        <form method="POST" action="{{ route('customer.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="profileName" class="form-label">Name</label>
                                    <input type="text" id="profileName" name="name" class="form-control"
                                        value="{{ old('name', auth()->user()->name) }}" required />
                                    @error('name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="profileUsername" class="form-label">Username</label>
                                    <input type="text" id="profileUsername" name="username" class="form-control"
                                        value="{{ old('username', auth()->user()->username) }}" required />
                                    @error('username')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="profileEmail" class="form-label">Email</label>
                                    <input type="email" id="profileEmail" name="email" class="form-control"
                                        value="{{ old('email', auth()->user()->email) }}" />
                                    @error('email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="profilePhone" class="form-label">Phone number</label>
                                    <input type="tel" id="profilePhone" name="phone" class="form-control"
                                        value="{{ old('phone', auth()->user()->phone) }}" />
                                    @error('phone')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="profilePassword" class="form-label">New password</label>
                                    <input type="password" id="profilePassword" name="password" class="form-control"
                                        autocomplete="new-password" />
                                    <small class="form-text text-muted">Leave blank to keep your current password.</small>
                                    @error('password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="profilePasswordConfirmation" class="form-label">Confirm new password</label>
                                    <input type="password" id="profilePasswordConfirmation" name="password_confirmation"
                                        class="form-control" autocomplete="new-password" />
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary rounded-pill py-3 px-5 text-white">
                                        Save changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
