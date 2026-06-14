@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Staff Management</h4>
            <p class="text-muted mb-0 small">View, manage, and monitor all staff members</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.staff-activities.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-activity me-1"></i> Activity Feed
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Staff ({{ $staff->count() }})</h5>
            <div class="input-group input-group-sm" style="max-width: 280px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="staffSearch" class="form-control border-start-0" placeholder="Search staff...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="staffTable">
                    <thead class="table-light">
                        <tr>
                            <th>Staff Member</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Assigned Categories</th>
                            <th>Recent Activities</th>
                            <th>Total Actions</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                        <tr class="staff-row">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-{{ $member->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                        <span class="fw-bold text-{{ $member->status === 'active' ? 'success' : 'secondary' }}" style="font-size: 0.85rem;">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">{{ $member->name }}</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">
                                            @if($member->staffProfile && $member->staffProfile->department)
                                                {{ $member->staffProfile->department }}
                                            @else
                                                Staff
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;">
                                    <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $member->email }}</div>
                                    @if($member->phone)
                                    <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $member->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($member->staffProfile && $member->staffProfile->department)
                                    <span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.8rem;">
                                        {{ $member->staffProfile->department }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($member->categoryAssignments as $assignment)
                                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.75rem;">
                                        {{ $assignment->category->name ?? 'Unknown' }}
                                    </span>
                                    @empty
                                    <span class="text-muted" style="font-size: 0.8rem;">No categories</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="max-width: 250px;">
                                @if($member->recent_activities->count() > 0)
                                <div style="font-size: 0.78rem; line-height: 1.6;">
                                    @foreach($member->recent_activities as $activity)
                                    <div class="text-truncate" style="max-width: 240px;">
                                        <span class="badge bg-light text-muted me-1" style="font-size: 0.65rem;">{{ $activity->created_at->format('H:i') }}</span>
                                        <span class="text-muted">{{ Str::limit($activity->description ?? $activity->action, 35) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-muted" style="font-size: 0.8rem;">No recent activity</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ $member->total_activities }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                    <i class="bi bi-{{ $member->status === 'active' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.staff-activities.index') }}?staff_id={{ $member->id }}" class="btn btn-sm btn-outline-info border-0" title="View Activities">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.staff-activities.edit', $member) }}" class="btn btn-sm btn-outline-primary border-0" title="Edit Staff">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.staff-activities.toggle-status', $member) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $member->status === 'active' ? 'warning' : 'success' }} border-0" 
                                                title="{{ $member->status === 'active' ? 'Deactivate Staff' : 'Activate Staff' }}"
                                                onclick="return confirm('Are you sure you want to {{ $member->status === 'active' ? 'deactivate' : 'activate' }} {{ $member->name }}?')">
                                            <i class="bi bi-{{ $member->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.staff-activities.destroy', $member) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" 
                                                title="Delete Staff"
                                                onclick="return confirm('Are you sure you want to permanently delete {{ $member->name }}? This action cannot be undone.');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-6 d-block mb-2 text-muted"></i>
                                <p class="mb-0">No staff members found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('staffSearch');
    const rows = document.querySelectorAll('.staff-row');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection