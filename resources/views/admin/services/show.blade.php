@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $service->name }}</h4>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Service Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Category:</strong> <span class="badge bg-secondary">{{ $service->category }}</span></div>
                    <div class="mb-2"><strong>Base Price:</strong> KES {{ number_format($service->base_price, 2) }}</div>
                    <div class="mb-2"><strong>Description:</strong> {{ $service->description ?? 'N/A' }}</div>
                    <hr>
                    <div><strong>Total Income:</strong> <span class="text-success">KES {{ number_format($totalIncome, 2) }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Service Requests ({{ $serviceRequests->total() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Assigned To</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceRequests as $request)
                                <tr>
                                    <td>#{{ $request->id }}</td>
                                    <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ ucfirst($request->status) }}</span></td>
                                    <td>KES {{ number_format($request->cost, 2) }}</td>
                                    <td>{{ $request->assignedStaff->name ?? 'Unassigned' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateRequestModal{{ $request->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="updateRequestModal{{ $request->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.services.update-request', $request->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Request #{{ $request->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="in_progress" {{ $request->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Cost</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">KES</span>
                                                            <input type="number" step="0.01" name="cost" value="{{ $request->cost }}" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Notes</label>
                                                        <textarea name="notes" class="form-control">{{ $request->notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($serviceRequests->hasPages())
                <div class="card-footer bg-white">{{ $serviceRequests->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection