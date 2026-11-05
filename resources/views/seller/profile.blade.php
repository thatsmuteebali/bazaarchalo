@extends('layouts.seller')
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
        <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" id="profile-form">
            @csrf
            @method('PUT')
            <div class="card border-light shadow-sm p-4 h-100">
                <!-- Text input -->
                <div class="row">

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mt-3">
                            <strong>There are errors with some fields. Please fix them and try again.</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="col-md-12 mb-3">
                        <label for="image" class="form-label-custom">Profile Image</label>
                        <div class="mb-2">
                            <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('assets/images/no_image.png') }}" alt="Current profile image" class="image-preview"
                                style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%;">
                        </div>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="form-control-custom image @error('image') is-invalid-custom @enderror">
                        <small class="text-muted">Leave empty to keep the current image. Maximum file size: 4 MB.</small>
                        @error('image')
                            <div class="form-feedback-custom invalid-custom">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

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
                        <label for="username" class="form-label-custom">Username</label>
                        <input type="username" name="username"
                            class="form-control-custom @error('username') is-invalid-custom @enderror" id="username"
                            value="{{ old('username', auth()->user()->username) }}" placeholder="Enter username" maxlength="255" >
                        @error('username')
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

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label-custom">Phone Number</label>
                        <input type="phone" name="phone"
                            class="form-control-custom @error('phone') is-invalid-custom @enderror" id="phone"
                            value="{{ old('phone', auth()->user()->phone) }}" placeholder="Enter phone" maxlength="255" >
                        @error('phone')
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
                    <a href="{{ route('seller.dashboard') }}" class="btn-custom btn-custom-light">Cancel</a>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('customjs')
<script>
    $(document).ready(function () {
        $("#profile-form").on("submit", function () {
            $('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@endsection
