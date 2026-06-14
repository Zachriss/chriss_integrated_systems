@extends('super-admin.layouts.super-admin')

@section('title', 'Reports')

@section('content')
<div class="sa-page-header">
    <h1>Reports</h1>
    <p>View system reports and analytics.</p>
</div>

<div class="row g-3">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super-admin.reports.users') }}" class="text-decoration-none">
            <div class="sa-card sa-stat text-center">
                <div class="sa-stat-icon mx-auto" style="background:rgba(26,115,232,0.1);color:#1a73e8;"><i class="bi bi-people"></i></div>
                <h5 style="font-size:0.95rem;font-weight:600;color:#0f172a;">Users Report</h5>
                <p style="font-size:0.82rem;color:#64748b;margin:0;">View detailed user data, filter by role and status.</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super-admin.reports.roles') }}" class="text-decoration-none">
            <div class="sa-card sa-stat text-center">
                <div class="sa-stat-icon mx-auto" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-shield-lock"></i></div>
                <h5 style="font-size:0.95rem;font-weight:600;color:#0f172a;">Roles Report</h5>
                <p style="font-size:0.82rem;color:#64748b;margin:0;">Roles and permissions overview.</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super-admin.reports.audit-logs') }}" class="text-decoration-none">
            <div class="sa-card sa-stat text-center">
                <div class="sa-stat-icon mx-auto" style="background:rgba(234,179,8,0.1);color:#ca8a04;"><i class="bi bi-journal-text"></i></div>
                <h5 style="font-size:0.95rem;font-weight:600;color:#0f172a;">Audit Logs Report</h5>
                <p style="font-size:0.82rem;color:#64748b;margin:0;">All system activities and changes.</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('super-admin.reports.system-usage') }}" class="text-decoration-none">
            <div class="sa-card sa-stat text-center">
                <div class="sa-stat-icon mx-auto" style="background:rgba(22,163,74,0.1);color:#16a34a;"><i class="bi bi-bar-chart"></i></div>
                <h5 style="font-size:0.95rem;font-weight:600;color:#0f172a;">System Usage</h5>
                <p style="font-size:0.82rem;color:#64748b;margin:0;">Overall platform statistics.</p>
            </div>
        </a>
    </div>
</div>
@endsection