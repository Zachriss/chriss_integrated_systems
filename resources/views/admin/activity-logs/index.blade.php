@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container py-4">
    <h5 class="mb-3">Activity Logs</h5>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-2"><label class="form-label small">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
                <div class="col-md-2"><label class="form-label small">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
                <div class="col-md-2"><label class="form-label small">Action Type</label><select name="action_type" class="form-select form-select-sm"><option value="">All</option>@foreach($actionTypes as $t)<option value="{{ $t }}" {{ request('action_type')===$t?'selected':''}}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>@endforeach</select></div>
                <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button><a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
            </form>
            @if($logs->isEmpty())<div class="text-center py-4 text-muted">No activity logs found.</div>@else
            <div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>User</th><th>Role</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
            <tbody>@foreach($logs as $log)<tr><td>{{ $log->user->name ?? 'N/A' }}</td><td>{{ $log->role }}</td><td><span class="badge bg-{{ match($log->action_type){'income'=>'success','expense'=>'danger','task_update'=>'info','login'=>'primary',default=>'secondary'} }}">{{ ucfirst(str_replace('_',' ',$log->action_type)) }}</span></td><td>{{ $log->description }}</td><td>{{ $log->created_at->format('M d, Y H:i') }}</td></tr>@endforeach</tbody></table></div>
            <div class="mt-3">{{ $logs->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection