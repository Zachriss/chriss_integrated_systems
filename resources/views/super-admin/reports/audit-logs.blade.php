@extends('super-admin.layouts.super-admin')

@section('title', 'Audit Logs Report')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div><h1>Audit Logs Report</h1><p>All system activities and changes.</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-sa-outline btn-outline-primary" href="{{ route('super-admin.reports.export', ['report' => 'audit-logs', 'format' => 'pdf'] + request()->query()) }}"><i class="bi bi-filetype-pdf me-1"></i> PDF</a>
        <a class="btn btn-sa-outline btn-outline-success" href="{{ route('super-admin.reports.export', ['report' => 'audit-logs', 'format' => 'excel'] + request()->query()) }}"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    </div>
</div>

<div class="sa-card mb-3">
    <div class="sa-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <option value="">All</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">From</label><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="col-md-2"><label class="form-label">To</label><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-sa-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead><tr><th>Actor</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->actor?->name ?? 'System' }}</td>
                        <td><span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;">{{ $log->action }}</span></td>
                        <td style="max-width:300px;">{{ $log->description }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $log->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
