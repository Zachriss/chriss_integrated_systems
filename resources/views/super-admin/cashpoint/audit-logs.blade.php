@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-activity me-2"></i>Cash Point Audit Logs</h4>
        <a href="{{ route('super-admin.cashpoint.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->date?->format('M d, Y') ?? $log->created_at->format('M d, Y') }}</td>
                            <td><strong>{{ $log->user?->name ?? 'System' }}</strong></td>
                            <td>
                                <span class="badge bg-{{ 
                                    str_contains($log->action_type, 'opening') ? 'success' : 
                                    (str_contains($log->action_type, 'closing') ? 'info' : 
                                    (str_contains($log->action_type, 'reset') ? 'warning' : 
                                    (str_contains($log->action_type, 'reopen') ? 'secondary' : 
                                    (str_contains($log->action_type, 'correction') ? 'danger' : 'primary'))) 
                                }}">
                                    {{ str_replace('cashpoint_', '', $log->action_type) }}
                                </span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->created_at->format('H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No audit logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="mt-3">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
</write_to_file>