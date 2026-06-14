@extends('super-admin.layouts.super-admin')

@section('title', 'Service Operations Dashboard')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Service Operations Dashboard</h1>
        <p>Monitor and manage service operations across all departments.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Active Services</p>
            <div class="sa-stat-icon"><i class="bi bi-gear-fill"></i></div>
            <div class="sa-stat-value">{{ App\Models\Service::count() }}</div>
            <small>registered services</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Pending Requests</p>
            <div class="sa-stat-icon"><i class="bi bi-clock-history"></i></div>
            <div class="sa-stat-value">{{ App\Models\ServiceRequest::where('status', 'pending')->count() }}</div>
            <small>awaiting processing</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">In Progress</p>
            <div class="sa-stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div class="sa-stat-value">{{ App\Models\ServiceRequest::where('status', 'in_progress')->count() }}</div>
            <small>currently being handled</small>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Completed</p>
            <div class="sa-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="sa-stat-value">{{ App\Models\ServiceRequest::where('status', 'completed')->count() }}</div>
            <small>finished requests</small>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="sa-card">
            <div class="sa-card-body p-0">
                <div class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="font-size:0.95rem;">Recent Service Requests</h5>
                    <a href="{{ route('super-admin.operations.dashboard') }}" class="btn btn-sm btn-sa-outline btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table sa-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(App\Models\ServiceRequest::with(['customer', 'service'])->latest()->take(10)->get() as $sr)
                            <tr>
                                <td>{{ $sr->id }}</td>
                                <td>{{ $sr->customer?->name ?? 'N/A' }}</td>
                                <td>{{ $sr->service?->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="sa-badge" style="background:{{ $sr->status === 'completed' ? '#dcfce7' : ($sr->status === 'in_progress' ? '#fef9c3' : '#fee2e2') }};color:{{ $sr->status === 'completed' ? '#166534' : ($sr->status === 'in_progress' ? '#854d0e' : '#991b1b') }};">
                                        {{ ucfirst(str_replace('_', ' ', $sr->status)) }}
                                    </span>
                                </td>
                                <td style="font-size:0.8rem;color:#64748b;">{{ $sr->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No service requests yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Status Summary</h5>
                @php
                    $total = max(App\Models\ServiceRequest::count(), 1);
                    $pending = App\Models\ServiceRequest::where('status', 'pending')->count();
                    $inProgress = App\Models\ServiceRequest::where('status', 'in_progress')->count();
                    $completed = App\Models\ServiceRequest::where('status', 'completed')->count();
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Pending</span>
                        <span>{{ $pending }}</span>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-danger" style="width: {{ ($pending / $total) * 100 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>In Progress</span>
                        <span>{{ $inProgress }}</span>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-warning" style="width: {{ ($inProgress / $total) * 100 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Completed</span>
                        <span>{{ $completed }}</span>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-success" style="width: {{ ($completed / $total) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection