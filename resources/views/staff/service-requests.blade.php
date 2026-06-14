@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        @if(isset($service))
            <h5>
                <i class="bi bi-gear me-2"></i>{{ $service->name }} — Requests
            </h5>
        @else
            <h5>Customer Service Requests</h5>
        @endif
        <div>
            @if(isset($service))
                <a href="{{ route('staff.services') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Services
                </a>
            @endif
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($serviceRequests->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-3"></i>
                    <p class="mt-2">No service requests assigned to you.</p>
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
                                <th>Seen</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRequests as $req)
                            @php
                                $phone = $req->customer->phone ?? '';
                                $custName = $req->customer->full_name ?? $req->customer->name ?? 'Customer';
                                $serviceName = $req->service->name ?? 'service';
                                $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
                                if (strlen($cleanedPhone) === 9) {
                                    $cleanedPhone = '255' . $cleanedPhone;
                                } elseif (strlen($cleanedPhone) === 10 && str_starts_with($cleanedPhone, '0')) {
                                    $cleanedPhone = '255' . substr($cleanedPhone, 1);
                                }
                                $waMessage = "Hello {$custName}, regarding your {$serviceName} request #{$req->id}.";
                            @endphp
                            <tr class="{{ is_null($req->seen_at) ? 'table-warning' : '' }}">
                                <td>#{{ $req->id }}</td>
                                <td>
                                    <strong>{{ $req->customer->full_name ?? $req->customer->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @if($phone)
                                        <small>{{ $phone }}</small>
                                        <div class="mt-1">
                                            <a href="https://wa.me/{{ $cleanedPhone }}?text={{ urlencode($waMessage) }}" target="_blank" class="btn btn-sm btn-success px-2 py-0" title="WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                            <a href="tel:{{ $phone }}" class="btn btn-sm btn-outline-primary px-2 py-0" title="Call">
                                                <i class="bi bi-telephone"></i>
                                            </a>
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
                                </td>
                                <td>
                                    @if($req->seen_at)
                                        <span class="text-success small"><i class="bi bi-check-circle-fill"></i> {{ $req->seen_at->format('M d') }}</span>
                                    @else
                                        <span class="text-warning small"><i class="bi bi-clock"></i> New</span>
                                    @endif
                                </td>
                                <td><small>{{ $req->created_at->format('M d, Y') }}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('staff.service-requests.show', $req->id) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$req->seen_at)
                                        <button class="btn btn-sm btn-outline-success mark-seen-btn" data-id="{{ $req->id }}" title="Mark Seen">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $serviceRequests->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    document.querySelectorAll('.mark-seen-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const row = this.closest('tr');

            fetch(`/staff/service-requests/${id}/mark-seen`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({theme: 'success', title: 'Marked', text: res.message, timer: 1000});
                    }
                    row.classList.remove('table-warning');
                    setTimeout(() => location.reload(), 800);
                }
            })
            .catch(() => {});
        });
    });
});
</script>
@endsection
