@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @php
                $filteredStaff = request('staff_id') ? \App\Models\User::find(request('staff_id')) : null;
            @endphp
            @if($filteredStaff)
                <h4 class="mb-1">
                    <i class="bi bi-eye me-2"></i> Activities: {{ $filteredStaff->name }}
                </h4>
                <p class="text-muted mb-0 small">Showing activity log for {{ $filteredStaff->email }}</p>
            @else
                <h4 class="mb-1">Staff Activity Feed</h4>
                <p class="text-muted mb-0 small">Monitor real-time actions performed by staff and admin</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.staff-activities.staff-list') }}" class="btn btn-outline-primary">
                <i class="bi bi-people me-1"></i> Staff Management
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                @if($filteredStaff)
                    <i class="bi bi-person-circle me-1"></i> {{ $filteredStaff->name }}'s Activity Log
                @else
                    Recent Activity Log
                @endif
            </h5>
            <span class="badge bg-primary">{{ $activities->total() }} total entries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td style="white-space: nowrap; font-size: 0.85rem;">
                                <i class="bi bi-clock me-1 text-muted"></i>{{ $activity->created_at->format('M d, Y H:i') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                        <span class="fw-bold text-primary" style="font-size: 0.75rem;">
                                            {{ strtoupper(substr($activity->actor->name ?? 'S', 0, 2)) }}
                                        </span>
                                    </div>
                                    <span class="fw-semibold" style="font-size: 0.88rem;">{{ $activity->actor->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'create' => 'success',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        'view' => 'light',
                                    ];
                                    $color = $actionColors[strtolower($activity->action)] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                    {{ $activity->action }}
                                </span>
                            </td>
                            <td style="max-width: 350px;">
                                <span class="text-muted" style="font-size: 0.85rem;">{{ Str::limit($activity->description ?? 'No details', 100) }}</span>
                                @if($activity->module)
                                <br><span class="badge bg-light text-muted" style="font-size: 0.7rem;">{{ $activity->module }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-activity display-6 d-block mb-2 text-muted"></i>
                                <p class="mb-0">No activities recorded yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
        <div class="card-footer bg-white">{{ $activities->links() }}</div>
        @endif
    </div>
</div>
@endsection