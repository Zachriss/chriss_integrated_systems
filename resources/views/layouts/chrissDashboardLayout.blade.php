<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $system_settings->system_name ?? 'Dashboard' }} | {{ $system_settings->system_short_name ?? 'CIS' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/rootcolor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissHeader.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissSidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissLayout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chrissPublicFooter.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $system_settings->primary_color ?? '#1a73e8' }};
            --secondary-color: {{ $system_settings->secondary_color ?? '#6c757d' }};
            --accent-color: {{ $system_settings->accent_color ?? '#0d6efd' }};
            --color-primary-500: {{ $system_settings->primary_color ?? '#1a73e8' }};
            --color-primary-600: {{ $system_settings->primary_color ?? '#1a73e8' }};
        }
    </style>
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body data-disable-navigation-overlay="1">
    @php
        $pageHeader = match (true) {
            request()->is('dashboard') || request()->routeIs('dashboard.index') => [
                'title' => 'System Dashboard',
                'subtitle' => 'Monitor your application performance and stats from one workspace.',
            ],
            request()->is('dashboard/notifications*') => [
                'title' => 'Direct Notifications',
                'subtitle' => 'Review alerts, updates, and messages for your account.',
            ],
            request()->is('dashboard/profile*') => [
                'title' => 'User Profile',
                'subtitle' => 'Manage your account information and preferences.',
            ],
            request()->is('dashboard/settings*') => [
                'title' => 'System Settings',
                'subtitle' => 'Adjust your application behavior and personal settings.',
            ],
            request()->is('erp-admin/dashboard') || request()->routeIs('admin.dashboard') => [
                'title' => 'Admin Dashboard',
                'subtitle' => 'Manage daily operations and track performance metrics.',
            ],
            request()->is('erp-admin/cash-points*') => [
                'title' => 'Cash Point Management',
                'subtitle' => 'Record and track cash balances for daily operations.',
            ],
            request()->is('erp-admin/services*') => [
                'title' => 'Services Management',
                'subtitle' => 'Manage assigned services and service requests.',
            ],
            request()->is('erp-admin/inventory*') => [
                'title' => 'Inventory Management',
                'subtitle' => 'Track products and stock levels.',
            ],
            request()->is('erp-admin/reports*') => [
                'title' => 'Reports',
                'subtitle' => 'View transaction and performance reports.',
            ],
            request()->is('erp-admin/staff-activities*') => [
                'title' => 'Staff Activities',
                'subtitle' => 'Monitor activities of assigned staff members.',
            ],
            request()->is('erp-admin/staff-category-assignments*') => [
                'title' => 'Staff Category Assignments',
                'subtitle' => 'Manage category assignments for staff members.',
            ],
            request()->is('staff/income/history') || request()->routeIs('staff.income.history') => [
                'title' => 'Income History',
                'subtitle' => 'View and manage all your recorded income.',
            ],
            request()->is('staff/income/*/edit') || request()->routeIs('staff.income.edit') => [
                'title' => 'Edit Income Record',
                'subtitle' => 'Update the details of this income record.',
            ],
            request()->is('staff*') => [
                'title' => 'Staff Panel',
                'subtitle' => 'Manage your assigned tasks and activities from this workspace.',
            ],
            default => [
                'title' => 'User Panel',
                'subtitle' => 'Manage your activities from this workspace.',
            ],
        };
    @endphp

    @include('components.chrissHeader')
    @include('components.chrissSidebar')

    <main class="dashboard-page-content" @if($pageHeader) data-dashboard-page-header="true" @endif>
        @if ($pageHeader)
            <div class="px-3 px-lg-4 pt-4">
                <section class="dashboard-shared-page-header">
                    <div class="dashboard-shared-page-header__content">
                        <x-chriss-page-header :title="$pageHeader['title']" :subtitle="$pageHeader['subtitle']" />
                    </div>

                    @hasSection('page_header_actions')
                        <div class="dashboard-shared-page-header__actions">
                            @yield('page_header_actions')
                        </div>
                    @endif
                </section>
            </div>
        @endif

        <div class="dashboard-page-body">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    <style>
        .dashboard-shared-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.4rem 0 1.1rem;
            margin-bottom: 1.2rem;
            border-bottom: 1px solid rgba(var(--color-primary-500-rgb), 0.18);
        }
        .dashboard-shared-page-header__content {
            min-width: 0;
            flex: 1 1 auto;
        }
        .dashboard-shared-page-header__actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        .chriss-shared-page-header__title {
            margin: 0;
            font-size: clamp(1.2rem, 1.5vw, 1.5rem);
            line-height: 1.25;
            font-weight: 700;
            color: var(--color-primary-600);
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .chriss-shared-page-header__subtitle {
            max-width: 760px;
            margin: 0.45rem 0 0;
            color: var(--color-slate-500);
            font-size: 0.85rem;
            line-height: 1.55;
        }
        .dashboard-page-content[data-dashboard-page-header="true"] .dashboard-page-body > .container:first-child > :is(h1, h2, h3, h4):first-child {
            display: none !important;
        }
        @media (max-width: 768px) {
            .dashboard-shared-page-header {
                flex-direction: column;
                align-items: stretch;
                padding-bottom: 1rem;
            }
            .dashboard-shared-page-header__actions {
                justify-content: flex-start;
                flex-wrap: wrap;
            }
            .chriss-shared-page-header__subtitle {
                font-size: 0.8rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/chrissSidebar.js') }}"></script>
    <script src="{{ asset('js/chrissButtonSpinner.js') }}"></script>
    <script src="{{ asset('js/chrissAlerts.js') }}"></script>
    @yield('scripts')

    @if (session('success') && ! View::hasSection('disable_success_swal'))
        <script>
            if (typeof showSystemAlert === 'function') {
                showSystemAlert({
                    theme: 'success',
                    title: 'Action completed',
                    text: @js(session('success')),
                    timer: 2600,
                    showConfirmButton: false
                });
            }
        </script>
    @endif

    @if (session('error'))
        <script>
            if (typeof showSystemAlert === 'function') {
                showSystemAlert({
                    theme: 'danger',
                    title: 'Something went wrong',
                    text: @js(session('error')),
                    showConfirmButton: true,
                    confirmButtonText: '<i class="bi bi-arrow-repeat me-1"></i> OK'
                });
            }
        </script>
    @endif

    @stack('scripts')
</body>
</html>
