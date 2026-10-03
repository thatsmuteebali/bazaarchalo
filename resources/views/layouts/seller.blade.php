<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazaar Chalo - Premium Bootstrap 5 Admin Dashboard Template</title>

    <!-- SEO Optimization -->
    <meta name="description" content="Bazaar Chalo - Premium Bootstrap 5 Admin Dashboard Template">
    <meta name="author" content="Bazaar Chalo Team">

    <!-- Favicon -->
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
    <!-- ==========================================
         START: Sidebar Component
         Highly polished, dark-green sticky navigation
         ========================================== -->
    @include('seller.sidebar')
    <!-- ==========================================
         END: Sidebar Component
         ========================================== -->

    <!-- ==========================================
         START: Main Content Area
         ========================================== -->
    <div class="main-wrapper">

        <!-- START: Top Navbar Component -->
        @include('seller.navbar')
        <!-- END: Top Navbar Component -->
        @include('dashboard-message')
        @yield('content')

        <!-- START: Footer Component -->
        <footer class="footer-custom">
            <div class="footer-left">
                <span class="footer-logo">
                    <i class="bi bi-asterisk"></i> Bazaar Chalo
                </span>
                <span class="footer-separator">|</span>
                <span class="footer-copy">&copy; 2026 Made with <i
                        class="bi bi-heart-fill text-danger footer-heart"></i> by<a
                        href="https://sparkadminpro.gumroad.com/" target="_blank">Bazaar Chalo</a>• Distributed by <a
                        href="https://www.themewagon.com/" target="_blank">ThemeWagon</a> </span>
            </div>
            <div class="footer-right">
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Overview</a></li>
                    <li><a href="#" class="footer-link">Statistics</a></li>
                    <li><a href="#" class="footer-link">Help & Documentation</a></li>
                    <li><a href="#" class="footer-link">Status <span class="status-dot"></span></a></li>
                </ul>
            </div>
        </footer>
        <!-- END: Footer Component -->

    </div>
    <!-- ==========================================
         END: Main Content Area
         ========================================== -->

        @include('shared.confirm-delete-modal')

    <!-- Local Third-Party Libraries Script dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

    <!-- Local dashboard interactions controller -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    @yield('customjs')
</body>

</html>
