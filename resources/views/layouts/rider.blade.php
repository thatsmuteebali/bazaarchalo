<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazaar Chalo Rider Panel</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    @yield('customCss')
</head>

<body>
    @php
        $webSetting = getWebsiteContent('setting');
    @endphp
    @include('rider.sidebar')

    <div class="main-wrapper">
        @include('rider.navbar')
        @include('dashboard-message')
        @yield('content')

        <footer class="footer-custom">
            <div class="footer-left">
                <span class="footer-copy">&copy; 2026 </span>
                <span class="footer-logo">Bazaar Chalo</span>
                <span class="footer-separator">|</span>
            </div>
        </footer>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    @yield('customjs')
</body>

</html>
