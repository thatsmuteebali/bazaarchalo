<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller - Create Shop</title>

    <!-- SEO Optimization -->
    <meta name="description" content="Login Screen - Bazaar Chalo Premium Bootstrap 5 Admin Dashboard Template">
    <meta name="author" content="Bazaar Chalo Team">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">

    <!-- Main Design System & Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <style>
        .btn-social:hover {
            color: black;
        }
    </style>
</head>

<body>
    @php
        $webSetting = getWebsiteContent('setting');
    @endphp
    <!-- ==========================================
         START: Authentication Container & Login Card
         ========================================== -->
    <div class="login-wrapper">
        <!-- Glowing background shapes for modern visual appearance -->
        <div class="login-bg-shape login-bg-shape-1"></div>
        <div class="login-bg-shape login-bg-shape-2"></div>

        <!-- Main centered login card -->
        <div class="login-card">

            <!-- Brand Identity -->
            <a href="#" class="login-brand text-decoration-none">
                {{-- <i class="bi bi-asterisk"></i> --}}
                <img src="{{ asset('storage/' . ltrim($webSetting['Website Header Logo'], '/')) }}"
                    class="site-logo site-logo-header" alt="Bazaar Chalo" width="50" height="50">
                <span>Bazaar Chalo</span>
            </a>
            @include('dashboard-message')
            <p class="login-subtitle">Please add your first shop</p>

            <!-- Login Form -->
            <form method="POST" action="{{ route('store-shop') }}" enctype="multipart/form-data">
                @csrf

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
                <div class="mb-3">
                    <label for="banner" class="form-label-custom">Shop Banner</label>

                    <input type="file" name="banner" id="banner" accept="image/*"
                        class="form-control-custom image @error('banner') is-invalid-custom @enderror">
                    <div class="mt-2">
                        <img src="{{ !empty($shop?->banner) ? asset('storage/' . $shop->banner) : asset('assets/images/no_image.png') }}"
                            alt="{{ !empty($shop?->banner) ? $shop->name : 'shop image' }}" class="image-preview"
                            style="width: 180px; height: 90px; object-fit: contain;">
                    </div>
                    <small class="text-muted">Optional. Maximum file size: 4 MB.</small>
                    @error('banner')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class=" mb-3">
                    <label for="name" class="form-label-custom">Shop Name</label>
                    <input type="text" name="name" id="name" maxlength="255" required
                        value="{{ old('name', $shop->name ?? '') }}"
                        class="form-control-custom @error('name') is-invalid-custom @enderror"
                        placeholder="Enter shop name">
                    @error('name')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class=" mb-3">
                    <label for="slug" class="form-label-custom">Slug</label>
                    <input type="text" name="slug"
                        class="form-control-custom @error('slug') is-invalid-custom @enderror" id="slug"
                        value="{{ old('slug', $shop->slug ?? '') }}" placeholder="eg: shop-name" maxlength="255">
                    <small class="text-muted">Slug must contain only lowercase letters, numbers, and single hyphens (e.g. apple-iphone-15)</small>
                    @error('slug')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label-custom">Description</label>
                    <textarea name="description" id="description" rows="4" maxlength="5000"
                        class="form-control-custom @error('description') is-invalid-custom @enderror" placeholder="Describe your shop">{{ old('description', $shop->description ?? '') }}</textarea>
                    @error('description')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label-custom">Address</label>
                    <textarea name="address" id="address" rows="3" maxlength="5000"
                        class="form-control-custom @error('address') is-invalid-custom @enderror" placeholder="Enter shop address">{{ old('address', $shop->address ?? '') }}</textarea>
                    @error('address')
                        <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class=" mb-3">
                    <input type="hidden" name="is_primary" value="0">
                    <label for=""></label>
                    <div class="form-switch-custom">
                        <input name="is_primary" value="1"
                            {{ old('is_primary', $shop->is_primary ?? false) ? 'checked' : '' }}
                            class="form-switch-input-custom" type="checkbox" id="is_primary">
                        <label class="form-switch-label" for="is_primary">Is Primary</label>
                    </div>
                </div>

                @section('customjs')
                    <script>
                        $(document).ready(function() {
                            $("#shop-form").on("submit", function() {
                                $('button[type="submit"]').prop('disabled', true);
                            });
                        });
                    </script>
                @endsection

                <button type="submit" class="btn-login" id="btn-submit">
                    <span>Create Shop</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <p class="login-footer-text mt-3">
                <a href="#" id="link-register"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Back to Home</a>
            </p>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

        </div>
    </div>
    <!-- END: Authentication Container -->

    <!-- Local Bootstrap bundle -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Custom Authentication interactions script -->
    <script src="{{ asset('assets/js/auth.js') }}"></script>

    <script>
        $(document).on("change", ".image", function(e) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $(".image-preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(e.target.files[0]);
        });
    </script>

</body>

</html>
