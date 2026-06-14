@extends('super-admin.layouts.super-admin')

@section('title', 'Activity Tracking')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Activity Tracking</h1>
        <p>Track and monitor user activities across the system.</p>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(App\Models\AuditTrail::with('actor')->latest()->take(25)->get() as $log)
                    <tr>
                        <td>
                            @if($log->actor)
                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $log->actor->name }}</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">{{ $log->actor->email }}</div>
                            @else
                            <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td><span class="sa-badge" style="background:#eef2ff;color:#4f46e5;">{{ $log->action }}</span></td>
                        <td>{{ $log->module ?? '-' }}</td>
                        <td style="max-width:250px;">{{ Str::limit($log->description, 80) }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $log->ip_address ?? '-' }}</td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No activities recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection