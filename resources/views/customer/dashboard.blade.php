@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <h5 class="mb-4">My Dashboard</h5>
    <div class="row g-3 mb-4">
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-primary"><div class="card-body p-3">
                <h6 class="text-white-50 small">Active Requests</h6>
                <h5 class="mb-0">{{ $activeRequests }}</h5>
            </div></div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-success"><div class="card-body p-3">
                <h6 class="text-white-50 small">Completed</h6>
                <h5 class="mb-0">{{ $completedRequests }}</h5>
            </div></div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-bg-info"><div class="card-body p-3">
                <h6 class="text-white-50 small">Assigned Products</h6>
                <h5 class="mb-0">{{ $assignedProducts }}</h5>
            </div></div>
        </div>
        <div class="col-3 col-md-3">
            <div class="card border-0 shadow-sm h-100 {{ $unreadResponses > 0 ? 'text-bg-warning' : 'text-bg-secondary' }}"><div class="card-body p-3">
                <h6 class="{{ $unreadResponses > 0 ? 'text-dark-emphasis' : 'text-white-50' }} small">Staff Responses</h6>
                <h5 class="mb-0">{{ $unreadResponses }}</h5>
                @if($unreadResponses > 0)
                    <a href="{{ route('customer.my-requests') }}" class="text-dark-emphasis small">View &raquo;</a>
                @endif
            </div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Service Requests</h5>
            <a href="{{ route('customer.my-requests') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recentRequests->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No service requests yet.</p>
                    <a href="{{ route('customer.services.index') }}" class="btn btn-primary btn-sm">Browse Services</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Service</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            @foreach($recentRequests as $req)
                            <tr>
                                <td>{{ $req->service->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ match($req->status){'pending'=>'warning','assigned'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger',default=>'secondary'} }}">{{ ucfirst(str_replace('_',' ',$req->status)) }}</span></td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-6"><a href="{{ route('customer.services.index') }}" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-gear me-1"></i> Browse Services</a></div>
        <div class="col-md-6"><a href="{{ route('customer.my-products') }}" class="btn btn-outline-info w-100 py-3"><i class="bi bi-box-seam me-1"></i> My Products</a></div>
    </div>
</div>
@endsection