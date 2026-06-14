@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>My Service Requests</h5>
        <a href="{{ route('customer.services.index') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i> New Request</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($requests->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-3"></i>
                    <p class="mt-2">No service requests yet.</p>
                    <a href="{{ route('customer.services.index') }}" class="btn btn-primary">Browse Services</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Service</th><th>Status</th><th>Staff</th><th>Staff Response</th><th>Notes</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr class="{{ $req->responded_at && !$req->seen_at ? 'table-info' : '' }}">
                                <td>#{{ $req->id }}</td>
                                <td>{{ $req->service->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ match($req->status){
                                        'pending'=>'warning','assigned'=>'info','in_progress'=>'primary',
                                        'completed'=>'success','cancelled'=>'danger',default=>'secondary'
                                    } }}">{{ ucfirst(str_replace('_',' ',$req->status)) }}</span>
                                </td>
                                <td>{{ $req->assignedStaff->name ?? 'Not assigned' }}</td>
                                <td>
                                    @if($req->staff_response)
                                        <span class="text-success small"><i class="bi bi-chat-dots-fill"></i> Responded</span>
                                        <small class="d-block text-muted">{{ Str::limit($req->staff_response, 40) }}</small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ Str::limit($req->notes, 50) }}</small></td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection