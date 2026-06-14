@extends('super-admin.layouts.super-admin')

@section('title', 'Audit Logs')

@section('content')
<div class="sa-page-header">
    <h1>Audit Logs</h1>
    <p>Track all system activities and changes.</p>
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
            <div class="col-md-3">
                <label class="form-label">Module</label>
                <select name="module" class="form-select">
                    <option value="">All</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sa-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead>
                    <tr><th>Actor</th><th>Action</th><th>Module</th><th>Description</th><th>IP</th><th>Date</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->actor?->name ?? 'System' }}</td>
                        <td><span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;">{{ $log->action }}</span></td>
                        <td>{{ $log->module ?? '—' }}</td>
                        <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->description }}</td>
                        <td style="font-size:0.8rem;">{{ $log->ip_address ?? '—' }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td><a href="{{ route('super-admin.audit-logs.show', $log) }}" class="btn btn-sm btn-sa-outline btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="sa-card-body border-top">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
