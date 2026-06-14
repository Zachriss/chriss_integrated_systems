@extends('super-admin.layouts.super-admin')

@section('title', 'System Overview')

@section('content')
<div class="sa-page-header">
    <h1>System Overview</h1>
    <p>View system information and health status.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">PHP Version</p>
            <div class="sa-stat-icon"><i class="bi bi-code-slash"></i></div>
            <div class="sa-stat-value" style="font-size:1.2rem;">{{ PHP_VERSION }}</div>
            <small>runtime environment</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Laravel Version</p>
            <div class="sa-stat-icon"><i class="bi bi-layers"></i></div>
            <div class="sa-stat-value" style="font-size:1.2rem;">{{ app()->version() }}</div>
            <small>framework version</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Environment</p>
            <div class="sa-stat-icon"><i class="bi bi-gear"></i></div>
            <div class="sa-stat-value" style="font-size:1.2rem;">{{ app()->environment() }}</div>
            <small>application mode</small>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Database</h5>
                <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                    <tr>
                        <td class="text-muted">Connection</td>
                        <td class="fw-semibold">{{ config('database.default') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Database Name</td>
                        <td class="fw-semibold">{{ config('database.connections.mysql.database') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Host</td>
                        <td class="fw-semibold">{{ config('database.connections.mysql.host') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Application</h5>
                <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                    <tr>
                        <td class="text-muted">APP_URL</td>
                        <td class="fw-semibold">{{ config('app.url') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Debug Mode</td>
                        <td class="fw-semibold">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cache Driver</td>
                        <td class="fw-semibold">{{ config('cache.default') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection