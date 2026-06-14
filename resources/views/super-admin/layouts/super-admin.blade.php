<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - {{ $system_settings->system_name ?? 'System' }}</title>
    <link rel="icon" type="image/png" href="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/rootcolor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissHeader.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissSidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissLayout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissPublicFooter.css') }}" rel="stylesheet">
    @stack('styles')
    <style>
        .dashboard-page-content {
            min-height: calc(100vh - var(--header-height) - var(--footer-height));
        }

        .dashboard-page-body {
            padding: 1.55rem 1.85rem 1.25rem;
        }

        .sa-page-header {
            margin-bottom: 1.15rem;
        }

        .sa-page-header h1 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #000;
            margin: 0;
        }

        .sa-page-header p {
            margin: 0.35rem 0 0;
            color: #506079;
            font-size: 0.78rem;
        }

        .sa-card {
            background: #fff;
            border: 1px solid #e0e4eb;
            border-radius: 10px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
        }

        .sa-card-body {
            padding: 1.1rem;
        }

        .sa-stat {
            min-height: 140px;
            padding: 1.1rem;
            position: relative;
        }

        .sa-stat-icon {
            position: absolute;
            top: 1.15rem;
            right: 1.15rem;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            background: #f4f6fb;
            color: #637cf4;
        }

        .sa-stat-value {
            margin-top: 2.65rem;
            color: #0d1b34;
            font-size: 1.55rem;
            font-weight: 900;
            line-height: 1.1;
        }

        .sa-stat-label {
            color: #667085;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
        }

        .sa-stat small {
            color: #667085;
            font-size: 0.72rem;
        }

        .sa-table {
            margin: 0;
        }

        .sa-table th {
            background: #f8fafc;
            color: #334155;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 0.85rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .sa-table td {
            padding: 0.82rem 0.85rem;
            font-size: 0.8rem;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
        }

        .sa-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .sa-badge-active { background: #dcfce7; color: #166534; }
        .sa-badge-inactive { background: #fef2f2; color: #991b1b; }
        .btn-sa-primary {
            background: #23bd82;
            border-color: #23bd82;
            color: #fff;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.55rem 1rem;
        }
        .btn-sa-primary:hover { background: #16a36e; border-color: #16a36e; color: #fff; }
        .btn-sa-outline {
            border-radius: 8px;
            font-size: 0.78rem;
            padding: 0.45rem 0.8rem;
        }
    </style>
</head>
<body data-disable-navigation-overlay="1">
    @section('panel_subtitle', 'Super Admin Control Center')
    @include('components.chrissHeader')
    @include('components.chrissSidebar')

    <main class="dashboard-page-content">
        <div class="dashboard-page-body">
            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/chrissSidebar.js') }}"></script>
    <script src="{{ asset('js/chrissButtonSpinner.js') }}"></script>
    @stack('scripts')
</body>
</html>
