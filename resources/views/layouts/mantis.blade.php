<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->
<head>
    <title>{{ \App\Models\Setting::get('site_name', config('app.name', 'Base CRM')) }} - @yield('title', 'Dashboard')</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ \App\Models\Setting::get('site_description', 'CRM Management System') }}">
    <meta name="keywords" content="CRM, Management, Dashboard, Admin">
    <meta name="author" content="Base CRM">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ \App\Models\Setting::get('site_favicon') ? asset(\App\Models\Setting::get('site_favicon')) : asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style-preset.css') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('layouts.parts.header-includes')
    
    <!-- Default Theme -->
    <style>
        .pc-sidebar {
            background-color: #ffffff !important;
        }
        .pc-header {
            background-color: #ffffff !important;
        }
    </style>
    
    <!-- Profile Dropdown Responsive Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    
    @stack('styles')
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    @include('layouts.parts.loader')
    @include('layouts.parts.sidebar')
    @include('layouts.parts.topbar')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('layouts.parts.modal')
    @include('layouts.parts.footer-includes')
    @include('layouts.parts.footer')
    @include('layouts.parts.footer-scripts')

    <!-- Required Js -->
    <script src="{{ asset('assets/mantis/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/fonts/custom-font.js') }}?v=2"></script>
    <script src="{{ asset('assets/mantis/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/feather.min.js') }}"></script>
    
    @stack('scripts')
</body>
<!-- [Body] end -->
</html>

