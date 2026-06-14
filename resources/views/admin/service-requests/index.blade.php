@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-inbox me-2"></i>Customer Orders / Service Requests</h4>
    </div>

    {{-- Status Count Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm text-bg-primary h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $counts['total'] }}</h3>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm text-bg-warning h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $counts['pending'] }}</h3>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm text-bg-info h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $counts['in_progress'] }}</h3>
                    <small>In Progress</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm text-bg-success h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $counts['completed'] }}</h3>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm text-bg-danger h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $counts['cancelled'] }}</h3>
                    <small>Cancelled</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.service-requests.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Service</label>
                    <select name="service_id" class="form-select form-select-sm">
                        <option value="">All Services</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ request('service_id') == $svc->id ? 'selected' : '' }}>{{ $svc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search (ID, Customer, Notes)</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('admin.service-requests.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Requests Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($serviceRequests->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-3"></i>
                    <p class="mt-2">No service requests found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRequests as $req)
                            <tr class="{{ $req->status === 'pending' ? 'table-warning' : ($req->status === 'completed' ? 'table-light' : '') }}">
                                <td>#{{ $req->id }}</td>
                                <td>
                                    <strong>{{ $req->customer->full_name ?? $req->customer->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @if($req->customer && $req->customer->phone)
                                        <small>{{ $req->customer->phone }}</small>
                                        @php
                                            $phone = $req->customer->phone;
                                            $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                            if (strlen($cleaned) === 9) { $cleaned = '255'.$cleaned; }
                                            elseif (strlen($cleaned) === 10 && str_starts_with($cleaned, '0')) { $cleaned = '255'.substr($cleaned, 1); }
                                        @endphp
                                        <div class="mt-1">
                                            <a href="https://wa.me/{{ $cleaned }}" target="_blank" class="btn btn-sm btn-success px-2 py-0" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                            <a href="tel:{{ $phone }}" class="btn btn-sm btn-outline-primary px-2 py-0" title="Call"><i class="bi bi-telephone"></i></a>
                                        </div>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>{{ $req->service->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ match($req->status) {
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                    </span>
                                    @if(!$req->seen_at && $req->status === 'pending')
                                        <span class="badge bg-danger ms-1">NEW</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->cost > 0)
                                        <strong>TZS {{ number_format($req->cost, 0) }}</strong>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>
                                    @if($req->assignedStaff)
                                        <small>{{ $req->assignedStaff->name }}</small>
                                    @else
                                        <small class="text-muted">Unassigned</small>
                                    @endif
                                </td>
                                <td><small>{{ $req->created_at->format('M d, Y') }}</small></td>
                                <td>
                                    <a href="{{ route('admin.service-requests.show', $req) }}" class="btn btn-sm btn-outline-primary" title="View & Manage">
                                        <i class="bi bi-eye"></i> Manage
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    {{ $serviceRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection