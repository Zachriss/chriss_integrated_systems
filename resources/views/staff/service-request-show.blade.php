@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><i class="bi bi-inbox me-2"></i>Service Request #{{ $serviceRequest->id }}</h5>
        <div>
            <a href="{{ route('staff.service-requests') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Requests
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Request Details --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Request Details</h6>
                    <span class="badge bg-{{ match($serviceRequest->status) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    } }} fs-6">
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
                    <div class="mb-3">
                        <small class="text-muted d-block">Description / Notes</small>
                        <p class="mb-0">{{ $serviceRequest->notes ?? 'No description provided.' }}</p>
                    </div>

                    @if($serviceRequest->problem_image_path)
                    <div class="mb-3">
                        <small class="text-muted d-block">Problem Image</small>
                        <a href="{{ asset('storage/' . $serviceRequest->problem_image_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="bi bi-image me-1"></i> View Image
                        </a>
                    </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Seen</small>
                            @if($serviceRequest->seen_at)
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> {{ $serviceRequest->seen_at->format('M d, Y g:i A') }}</span>
                            @else
                                <span class="text-warning"><i class="bi bi-clock"></i> Not yet seen</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Assigned Staff</small>
                            <strong>{{ $serviceRequest->assignedStaff->name ?? 'Unassigned' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Cost</small>
                            <strong>TZS {{ number_format($serviceRequest->cost ?? 0, 0) }}</strong>
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

            {{-- Status & Response Form --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-dots me-1"></i> Take Action</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <button class="btn btn-outline-success w-100 mark-seen-btn" data-id="{{ $serviceRequest->id }}" {{ $serviceRequest->seen_at ? 'disabled' : '' }}>
                                <i class="bi bi-eye{{ $serviceRequest->seen_at ? '-fill' : '' }} me-1"></i>
                                {{ $serviceRequest->seen_at ? 'Seen' : 'Mark as Seen' }}
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#respondModal">
                                <i class="bi bi-reply me-1"></i> Respond
                            </button>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select status-select" data-id="{{ $serviceRequest->id }}">
                                <option value="pending" {{ $serviceRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $serviceRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $serviceRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $serviceRequest->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    {{-- Customer Contact Actions --}}
                    @if($serviceRequest->customer)
                    <hr>
                    <h6 class="mb-2"><i class="bi bi-telephone me-1"></i> Contact Customer</h6>
                    <div class="d-flex gap-2">
                        @php
                            $phone = $serviceRequest->customer->phone;
                            $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
                            if (strlen($cleanedPhone) === 9) {
                                $cleanedPhone = '255' . $cleanedPhone;
                            } elseif (strlen($cleanedPhone) === 10 && str_starts_with($cleanedPhone, '0')) {
                                $cleanedPhone = '255' . substr($cleanedPhone, 1);
                            }
                            $message = "Hello {$serviceRequest->customer->name}, regarding your service request #{$serviceRequest->id} for {$serviceRequest->service->name}. Status: " . ucfirst(str_replace('_', ' ', $serviceRequest->status)) . ".";
                        @endphp
                        <a href="https://wa.me/{{ $cleanedPhone }}?text={{ urlencode($message) }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="tel:{{ $phone }}" class="btn btn-outline-primary">
                            <i class="bi bi-telephone me-1"></i> Call
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Customer Info --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person me-1"></i> Customer</h6>
                </div>
                <div class="card-body">
                    @if($serviceRequest->customer)
                        <h5>{{ $serviceRequest->customer->full_name ?? $serviceRequest->customer->name }}</h5>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td>
                                    <strong>{{ $serviceRequest->customer->phone }}</strong>
                                    <div class="mt-1">
                                        <a href="https://wa.me/{{ $cleanedPhone ?? '255' . ltrim($serviceRequest->customer->phone, '0') }}" target="_blank" class="btn btn-sm btn-success me-1">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                        <a href="tel:{{ $serviceRequest->customer->phone }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-telephone"></i>
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
        </div>
    </div>
</div>

{{-- Respond Modal --}}
<div class="modal fade" id="respondModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Respond to Request #{{ $serviceRequest->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="respondForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Your Response *</label>
                        <textarea name="staff_response" class="form-control" rows="4" required placeholder="Write your response to the customer..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Status (optional)</label>
                        <select name="status" class="form-select">
                            <option value="">Keep current status</option>
                            <option value="in_progress" {{ $serviceRequest->status === 'pending' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="respondSubmitBtn">Send Response</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Mark as Seen
    document.querySelector('.mark-seen-btn')?.addEventListener('click', function() {
        const btn = this;
        const id = btn.dataset.id;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';

        fetch(`/staff/service-requests/${id}/mark-seen`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({theme: 'success', title: 'Done', text: res.message, timer: 1500});
                }
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-eye me-1"></i> Mark as Seen';
        });
    });

    // Respond
    document.getElementById('respondForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('respondSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

        const formData = Object.fromEntries(new FormData(this).entries());

        fetch(`/staff/service-requests/{{ $serviceRequest->id }}/respond`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
            body: JSON.stringify(formData)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({theme: 'success', title: 'Sent', text: res.message, timer: 1500});
                }
                bootstrap.Modal.getInstance(document.getElementById('respondModal'))?.hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(res.message || 'Error');
                btn.disabled = false;
                btn.innerHTML = 'Send Response';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = 'Send Response';
            alert('Error sending response');
        });
    });

    // Status dropdown change
    document.querySelector('.status-select')?.addEventListener('change', function() {
        const id = this.dataset.id;
        const status = this.value;

        fetch(`/staff/service-requests/${id}/update-status`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
            body: JSON.stringify({status: status})
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({theme: 'success', title: 'Updated', text: res.message, timer: 1500});
                }
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(() => {});
    });
});
</script>
@endsection