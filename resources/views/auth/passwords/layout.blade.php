<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') | Bazaar Chalo</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
</head>

<body>
    @php
        $role = $role ?? 'seller';
        $loginRoute = match ($role) {
            'admin' => route('admin.login'),
            default => route('seller.login'),
        };
        $webSetting = getWebsiteContent('setting');
    @endphp
    <main class="login-wrapper">
        <div class="login-bg-shape login-bg-shape-1" aria-hidden="true"></div>
        <div class="login-bg-shape login-bg-shape-2" aria-hidden="true"></div>

        <section class="login-card" aria-labelledby="password-page-title">
            <a href="#" class="login-brand text-decoration-none">
                <img src="{{ asset('storage/'.ltrim($webSetting['Website Header Logo'], '/')) }}" class="site-logo site-logo-header" alt="Bazaar Chalo" width="50" height="50">
                <span>Bazaar Chalo</span>
            </a>

            <div class="text-center mb-4">
                <h1 id="password-page-title" class="h4 mb-2">@yield('title')</h1>
                <p class="login-subtitle mb-0">@yield('subtitle')</p>
            </div>

            @yield('form')

            <p class="login-footer-text mt-4 mb-0 text-center">
                Remembered your password?
                <a href="{{ $loginRoute }}">Back to sign in</a>
            </p>
        </section>
    </main>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
</body>

</html>
