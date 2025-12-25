<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @php
            $appFavicon = app_setting('app_favicon');
            $faviconHref = $appFavicon ? asset('storage/' . $appFavicon) : asset('mazer/assets/compiled/svg/favicon.svg');
        @endphp

        <link rel="shortcut icon" href="{{ $faviconHref }}">
        <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app-dark.css') }}">
        <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/auth.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>
    <style>
        body {
            background-color: #EDFBE2 !important;
        }
        
        [data-bs-theme=dark] body {
            background-color: #1F291F !important;
        }

        :root {
            --bs-primary: #316738;
            --bs-link-color: #316738;
            --bs-link-hover-color: #26522c;
        }

        .btn-primary {
            --bs-btn-bg: #316738;
            --bs-btn-border-color: #316738;
            --bs-btn-hover-bg: #26522c;
            --bs-btn-hover-border-color: #26522c;
            --bs-btn-active-bg: #26522c;
            --bs-btn-active-border-color: #26522c;
            --bs-btn-disabled-bg: #316738;
            --bs-btn-disabled-border-color: #316738;
        }

        .btn-outline-primary {
            --bs-btn-color: #316738;
            --bs-btn-border-color: #316738;
            --bs-btn-hover-bg: #316738;
            --bs-btn-hover-border-color: #316738;
            --bs-btn-active-bg: #316738;
            --bs-btn-active-border-color: #316738;
        }

        .text-primary {
            color: #316738 !important;
        }

        .bg-primary {
            background-color: #316738 !important;
        }

        .sidebar-wrapper .menu .sidebar-item.active > .sidebar-link {
            background-color: #316738;
        }


        .page-item.active .page-link {
            background-color: #316738;
            border-color: #316738;
        }

        #auth #auth-right {
            background: url(./png/4853433.png), linear-gradient(90deg, #316738, #26522c) !important;
        }

        [data-bs-theme=dark] #auth #auth-right {
            background: url(./png/4853433.png), linear-gradient(90deg, #316738, #26522c) !important;
        }
    </style>
    </head>
    <body>
        <div id="auth">
            {{ $slot }}
        </div>
    </body>
</html>
