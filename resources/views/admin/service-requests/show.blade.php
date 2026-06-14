@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-inbox me-2"></i>Service Request #{{ $serviceRequest->id }}</h4>
        <a href="{{ route('admin.service-requests.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Requests
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left: Request Details & Management --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Request Details</h5>
                    <span class="badge bg-{{ match($serviceRequest->status) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    } }} fs-6 px-3 py-2">
                        {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Service</small>
                            <strong>{{ $serviceRequest->service->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Date Requested</small>
                            <strong>{{ $serviceRequest->created_at->format('M d, Y g:i A') }}</strong>
                        </div>
                    </div>

                    @if($serviceRequest->service->category)
                    <div class="mb-3">
                        <small class="text-muted d-block">Category</small>
                        <strong>{{ $serviceRequest->service->category->name }}</strong>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block">Customer Notes</small>
                        <div class="bg-light p-3 rounded-3">
                            <p class="mb-0">{{ $serviceRequest->notes ?? 'No description provided.' }}</p>
                        </div>
                    </div>

                    @if($serviceRequest->problem_image_path)
                    <div class="mb-3">
                        <small class="text-muted d-block">Attached Image</small>
                        <a href="{{ asset('storage/' . $serviceRequest->problem_image_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="bi bi-image me-1"></i> View Image
                        </a>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Seen</small>
                            @if($serviceRequest->seen_at)
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> {{ $serviceRequest->seen_at->format('M d, Y g:i A') }}</span>
                            @else
                                <span class="text-warning"><i class="bi bi-clock"></i> Not yet seen</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Assigned Staff</small>
                            <strong>{{ $serviceRequest->assignedStaff->name ?? 'Unassigned' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Cost</small>
                            <strong>TZS {{ number_format($serviceRequest->cost ?? 0, 0) }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Processed By</small>
                            <strong>{{ $serviceRequest->processedBy->name ?? 'Not processed' }}</strong>
                            @if($serviceRequest->processed_at)
                                <br><small class="text-muted">{{ $serviceRequest->processed_at->format('M d, Y g:i A') }}</small>
                            @endif
                        </div>
                    </div>

                    @if($serviceRequest->staff_response)
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted d-block">Staff Response</small>
                        <div class="bg-light p-3 rounded-3">
                            <p class="mb-1">{{ $serviceRequest->staff_response }}</p>
                            @if($serviceRequest->responded_at)
                                <small class="text-muted">— {{ $serviceRequest->responded_at->format('M d, Y g:i A') }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Update Form --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-1"></i> Manage Request</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.service-requests.update', $serviceRequest) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $serviceRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $serviceRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $serviceRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $serviceRequest->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cost (TZS)</label>
                                <input type="number" name="cost" class="form-control" value="{{ $serviceRequest->cost }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Assign to Staff</label>
                                <select name="assigned_staff_id" class="form-select staff-select">
                                    <option value="">— Unassigned —</option>
                                    @foreach(\App\Models\User::where('role', 'staff')->where('status', 'active')->orderBy('name')->get() as $staff)
                                        <option value="{{ $staff->id }}" {{ $serviceRequest->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Staff Response (optional)</label>
                                <textarea name="staff_response" class="form-control" rows="3" placeholder="Write a response to the customer...">{{ $serviceRequest->staff_response }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Admin Notes / Update Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $serviceRequest->notes }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Customer Info & Contact --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-1"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    @if($serviceRequest->customer)
                        <h5>{{ $serviceRequest->customer->full_name ?? $serviceRequest->customer->name }}</h5>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>
                                    <strong>{{ $serviceRequest->customer->phone }}</strong>
                                    @php
                                        $phone = $serviceRequest->customer->phone;
                                        $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                        if (strlen($cleaned) === 9) { $cleaned = '255'.$cleaned; }
                                        elseif (strlen($cleaned) === 10 && str_starts_with($cleaned, '0')) { $cleaned = '255'.substr($cleaned, 1); }
                                        $msg = "Hello " . ($serviceRequest->customer->full_name ?? $serviceRequest->customer->name) . ", regarding your service request #{$serviceRequest->id} for {$serviceRequest->service->name}. Status: " . ucfirst(str_replace('_', ' ', $serviceRequest->status)) . ".";
                                    @endphp
                                    <div class="mt-2 d-flex gap-2">
                                        <a href="https://wa.me/{{ $cleaned }}?text={{ urlencode($msg) }}" target="_blank" class="btn btn-success btn-sm">
                                            <i class="bi bi-whatsapp"></i> WhatsApp
                                        </a>
                                        <a href="tel:{{ $serviceRequest->customer->phone }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-telephone"></i> Call
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @if($serviceRequest->customer->email)
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $serviceRequest->customer->email }}</td>
                            </tr>
                            @endif
                            @if($serviceRequest->customer->address)
                            <tr>
                                <td class="text-muted">Address</td>
                                <td>{{ $serviceRequest->customer->address }}</td>
                            </tr>
                            @endif
                        </table>
                    @else
                        <p class="text-muted mb-0">Customer information not available.</p>
                    @endif
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-1"></i> Activity Log</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $logs = \App\Models\ActivityLog::where('reference_id', $serviceRequest->id)
                            ->where(function($q) {
                                $q->where('action_type', 'service_request')
                                  ->orWhere('action_type', 'service_request_response')
                                  ->orWhere('action_type', 'service_request_status_update')
                                  ->orWhere('action_type', 'product_order')
                                  ->orWhere('action_type', 'admin_service_request_update');
                            })
                            ->with('user')
                            ->orderByDesc('created_at')
                            ->limit(10)
                            ->get();
                    @endphp
                    @forelse($logs as $log)
                        <div class="px-3 py-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <small><strong>{{ $log->user->name ?? 'System' }}</strong></small>
                                <small class="text-muted">{{ $log->created_at->format('M d, g:i A') }}</small>
                            </div>
                            <small class="text-muted d-block">{{ $log->description }}</small>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3">
                            <small>No activity logged.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection