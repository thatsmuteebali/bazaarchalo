@extends('layouts.admin')
@section('customCss')
@endsection

@section('content')
    <!-- START: Page Header Banner -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Profile</h1>
            <p class="page-subtitle">Manage Profile.</p>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <!-- Column 1: Basic controls -->
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card border-light shadow-sm p-4 h-100">
                <!-- Text input -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label-custom">Name</label>
                        <input type="text" name="name"
                            class="form-control-custom @error('name') is-invalid-custom @enderror" id="name"
                            value="{{ old('name', auth()->user()->name) }}" placeholder="Enter Name" maxlength="255" >
                        @error('name')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label-custom">Email</label>
                        <input type="email" name="email"
                            class="form-control-custom @error('email') is-invalid-custom @enderror" id="email"
                            value="{{ old('email', auth()->user()->email) }}" placeholder="Enter Email" maxlength="255" >
                        @error('email')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">

                        <div class="login-form-group">
                            <label for="password" class="login-form-label">Password</label>

                            <div class="login-input-group">
                                <i class="bi bi-shield-lock input-icon"></i>

                                <input type="password" name="password" id="password"
                                    class="login-input login-input-password" placeholder="••••••••">

                                <button type="button" class="password-toggle-btn" data-target="password"
                                    aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Leave blank to keep your current password.</small>
                            @error('password')
                                <div class="form-feedback-custom invalid-custom">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>


                    <div class="col-md-6">
                        <!-- Confirm Password Input Group -->
                        <div class="login-form-group">
                            <label for="password_confirmation" class="login-form-label">
                                Confirm Password
                            </label>

                            <div class="login-input-group">
                                <i class="bi bi-shield-lock input-icon"></i>

                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="login-input login-input-password" placeholder="••••••••">

                                <button type="button" class="password-toggle-btn" data-target="password_confirmation"
                                    aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            @error('password_confirmation')
                                <div class="form-feedback-custom invalid-custom">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>



                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button class="btn-custom btn-custom-primary" type="submit">Save</button>
                    <a href="{{ route('admin.categories.list') }}" class="btn-custom btn-custom-light">Cancel</a>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('customjs')
@endsection
