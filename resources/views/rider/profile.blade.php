@extends('layouts.rider')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Profile</h1>
            <p class="page-subtitle">Manage your rider account details.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('rider.profile.update') }}" enctype="multipart/form-data" id="rider-profile-form">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <strong>Please correct the fields below and try again.</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li class="small">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="image" class="form-label-custom">Profile Image</label>
                    <div class="mb-2">
                        <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : asset('assets/images/no_image.png') }}"
                            alt="Current profile image" class="image-preview"
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
                    <input type="text" name="name" id="name"
                        class="form-control-custom @error('name') is-invalid-custom @enderror"
                        value="{{ old('name', auth()->user()->name) }}" maxlength="255" required>
                    @error('name')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label-custom">Username</label>
                    <input type="text" name="username" id="username"
                        class="form-control-custom @error('username') is-invalid-custom @enderror"
                        value="{{ old('username', auth()->user()->username) }}" maxlength="50" required>
                    @error('username')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label-custom">Email</label>
                    <input type="email" name="email" id="email"
                        class="form-control-custom @error('email') is-invalid-custom @enderror"
                        value="{{ old('email', auth()->user()->email) }}" maxlength="255" required>
                    @error('email')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label-custom">Phone Number</label>
                    <input type="tel" name="phone" id="phone"
                        class="form-control-custom @error('phone') is-invalid-custom @enderror"
                        value="{{ old('phone', auth()->user()->phone) }}" placeholder="03000000000"
                        pattern="03[0-9]{9}" maxlength="11" required>
                    @error('phone')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label-custom">New Password</label>
                    <input type="password" name="password" id="password"
                        class="form-control-custom @error('password') is-invalid-custom @enderror"
                        autocomplete="new-password">
                    <small class="text-muted">Leave blank to keep your current password.</small>
                    @error('password')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label-custom">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control-custom" autocomplete="new-password">
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn-custom btn-custom-primary" type="submit">Save Profile</button>
                <a href="{{ route('rider.dashboard') }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection

@section('customjs')
    <script>
        $(function () {
            $('#rider-profile-form').on('submit', function () {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });
        });
    </script>
@endsection
