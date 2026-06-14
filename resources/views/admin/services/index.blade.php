@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Services Management</h4>
        <a href="{{ route('admin.services.income-report') }}" class="btn btn-outline-primary">
            <i class="bi bi-graph-up me-1"></i> Income Report
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Assigned Services</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Base Price</th>
                            <th>Requests</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td><span class="badge bg-secondary">{{ $service->category }}</span></td>
                            <td>KES {{ number_format($service->base_price, 2) }}</td>
                            <td>{{ $service->service_requests_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No services assigned.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($services->hasPages())
        <div class="card-footer bg-white">{{ $services->links() }}</div>
        @endif
    </div>
</div>
@endsection