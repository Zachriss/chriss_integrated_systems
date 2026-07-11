<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auth') | {{ $system_settings->system_short_name ?? 'CIS' }}</title>
    <link rel="icon" type="image/png" href="{{ $system_settings && $system_settings->system_favicon ? asset('storage/' . $system_settings->system_favicon) : asset('img/logo.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/rootcolor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissAuth.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $system_settings->primary_color ?? '#1a73e8' }};
            --secondary-color: {{ $system_settings->secondary_color ?? '#6c757d' }};
            --accent-color: {{ $system_settings->accent_color ?? '#0d6efd' }};
        }
    </style>
</head>

<body class="auth-layout-body" data-disable-navigation-overlay="1" data-inline-spinner-links="1">
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/chrissButtonSpinner.js') }}"></script>
    @yield('scripts')
</body>

</html>