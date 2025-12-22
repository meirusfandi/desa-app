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
    </head>
    <body>
        <div id="auth">
            {{ $slot }}
        </div>
    </body>
</html>
