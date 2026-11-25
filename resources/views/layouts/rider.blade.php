<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazaar Chalo Rider Panel</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">

    <!-- Main Design System & Custom Stylesheet -->
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    {{-- datatable --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

    <script src="{{ asset('assets/js/auth.js') }}"></script>

    <!-- Local dashboard interactions controller -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    @yield('customjs')
</body>

</html>
