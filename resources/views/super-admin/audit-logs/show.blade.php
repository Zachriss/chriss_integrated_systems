@extends('super-admin.layouts.super-admin')

@section('title', 'Audit Log Detail')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Audit Log Detail</h1>
        <p>Inspect a recorded system activity.</p>
    </div>
    <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-sa-outline btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Logs</a>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="text-muted small">Actor</label>
                <div class="fw-semibold">{{ $log->actor?->name ?? $log->actor_name ?? 'System' }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Action</label>
                <div><span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;">{{ $log->action }}</span></div>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Date</label>
                <div>{{ $log->created_at->format('d M Y H:i:s') }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Module</label>
                <div>{{ $log->module ?? 'General' }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">IP Address</label>
                <div>{{ $log->ip_address ?? 'Not captured' }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Status Code</label>
                <div>{{ $log->status_code ?? 'N/A' }}</div>
            </div>
            <div class="col-12">
                <label class="text-muted small">Description</label>
                <div>{{ $log->description ?? 'No description provided.' }}</div>
            </div>
            <div class="col-12">
                <label class="text-muted small">User Agent</label>
                <div style="word-break:break-word;">{{ $log->user_agent ?? 'Not captured' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
