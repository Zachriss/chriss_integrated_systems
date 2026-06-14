@extends('super-admin.layouts.super-admin')

@section('title', 'System Usage Report')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div><h1>System Usage</h1><p>Overall platform statistics and usage metrics.</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-sa-outline btn-outline-primary" href="{{ route('super-admin.reports.export', ['report' => 'system-usage', 'format' => 'pdf']) }}"><i class="bi bi-filetype-pdf me-1"></i> PDF</a>
        <a class="btn btn-sa-outline btn-outline-success" href="{{ route('super-admin.reports.export', ['report' => 'system-usage', 'format' => 'excel']) }}"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <div class="sa-stat-icon" style="background:rgba(26,115,232,0.1);color:#1a73e8;"><i class="bi bi-people"></i></div>
            <div class="sa-stat-value">{{ $totalUsers }}</div>
            <div class="sa-stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <div class="sa-stat-icon" style="background:rgba(22,163,74,0.1);color:#16a34a;"><i class="bi bi-person-check"></i></div>
            <div class="sa-stat-value">{{ $activeUsers }}</div>
            <div class="sa-stat-label">Active Users ({{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}%)</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <div class="sa-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-journal-text"></i></div>
            <div class="sa-stat-value">{{ $totalLogs }}</div>
            <div class="sa-stat-label">Total Audit Logs</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-semibold mb-3" style="font-size:0.95rem;">Users by Role</h5>
                @forelse($usersByRole as $role => $count)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="text-transform:capitalize;">{{ str_replace('_', ' ', $role) }}</span>
                    <span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;">{{ $count }}</span>
                </div>
                @empty
                <p class="text-muted mb-0" style="font-size:0.88rem;">No data available.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-semibold mb-3" style="font-size:0.95rem;">Activity by Module</h5>
                @forelse($logsByModule as $module => $count)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $module }}</span>
                    <span class="sa-badge" style="background:rgba(139,92,246,0.1);color:#8b5cf6;">{{ $count }}</span>
                </div>
                @empty
                <p class="text-muted mb-0" style="font-size:0.88rem;">No data available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
