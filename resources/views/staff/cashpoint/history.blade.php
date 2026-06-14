@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Session History</h4>
        <a href="{{ route('staff.cashpoint.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Opening Cash</th>
                            <th class="text-end">Closing Cash</th>
                            <th class="text-end">Cash Diff</th>
                            <th class="text-end">M-Pesa Diff</th>
                            <th class="text-end">Airtel Diff</th>
                            <th class="text-end">Mixx Diff</th>
                            <th class="text-end">HaloPesa Diff</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $s)
                        <tr>
                            <td>{{ $s->session_date->format('M d, Y') }}</td>
                            <td class="text-end">TZS {{ number_format($s->opening_cash, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($s->closing_cash, 0) }}</td>
                            <td class="text-end {{ $s->cash_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s->cash_difference >= 0 ? '+' : '' }}{{ number_format($s->cash_difference, 0) }}
                            </td>
                            <td class="text-end {{ $s->mpesa_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s->mpesa_difference >= 0 ? '+' : '' }}{{ number_format($s->mpesa_difference, 0) }}
                            </td>
                            <td class="text-end {{ $s->airtel_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s->airtel_difference >= 0 ? '+' : '' }}{{ number_format($s->airtel_difference, 0) }}
                            </td>
                            <td class="text-end {{ $s->mixx_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s->mixx_difference >= 0 ? '+' : '' }}{{ number_format($s->mixx_difference, 0) }}
                            </td>
                            <td class="text-end {{ $s->halopesa_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $s->halopesa_difference >= 0 ? '+' : '' }}{{ number_format($s->halopesa_difference, 0) }}
                            </td>
                            <td>
                                @if($s->status === 'Closed')
                                    <span class="badge bg-success">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Open</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-session" data-session-id="{{ $s->id }}" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No sessions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($sessions->hasPages())
    <div class="mt-3">
        {{ $sessions->links() }}
    </div>
    @endif
</div>

{{-- Session Details Modal --}}
<div class="modal fade" id="sessionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Session Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sessionDetailBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    $('.view-session').on('click', function() {
        const sessionId = $(this).data('session-id');
        const modal = $('#sessionDetailModal');
        const body = $('#sessionDetailBody');
        body.html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.modal('show');

        $.ajax({
            url: '{{ url("/staff/cashpoint/session") }}/' + sessionId,
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.success) {
                    const s = response.session;
                    const sum = response.summary;
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date:</strong> ${s.session_date}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong> <span class="badge bg-${s.status === 'Closed' ? 'success' : 'warning'}">${s.status}</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Opening</th>
                                        <th class="text-end">Closing</th>
                                        <th class="text-end">Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Cash Drawer</strong></td>
                                        <td class="text-end">TZS ${numberFormat(sum.cash.opening)}</td>
                                        <td class="text-end">TZS ${numberFormat(sum.cash.closing)}</td>
                                        <td class="text-end ${sum.cash.difference >= 0 ? 'text-success' : 'text-danger'}">${sum.cash.difference >= 0 ? '+' : ''}${numberFormat(sum.cash.difference)}</td>
                                    </tr>
                                    <tr>
                                        <td>M-Pesa Float</td>
                                        <td class="text-end">TZS ${numberFormat(sum.mpesa.opening)}</td>
                                        <td class="text-end">TZS ${numberFormat(sum.mpesa.closing)}</td>
                                        <td class="text-end ${sum.mpesa.difference >= 0 ? 'text-success' : 'text-danger'}">${sum.mpesa.difference >= 0 ? '+' : ''}${numberFormat(sum.mpesa.difference)}</td>
                                    </tr>
                                    <tr>
                                        <td>Airtel Money Float</td>
                                        <td class="text-end">TZS ${numberFormat(sum.airtel.opening)}</td>
                                        <td class="text-end">TZS ${numberFormat(sum.airtel.closing)}</td>
                                        <td class="text-end ${sum.airtel.difference >= 0 ? 'text-success' : 'text-danger'}">${sum.airtel.difference >= 0 ? '+' : ''}${numberFormat(sum.airtel.difference)}</td>
                                    </tr>
                                    <tr>
                                        <td>Mixx by Yas Float</td>
                                        <td class="text-end">TZS ${numberFormat(sum.mixx.opening)}</td>
                                        <td class="text-end">TZS ${numberFormat(sum.mixx.closing)}</td>
                                        <td class="text-end ${sum.mixx.difference >= 0 ? 'text-success' : 'text-danger'}">${sum.mixx.difference >= 0 ? '+' : ''}${numberFormat(sum.mixx.difference)}</td>
                                    </tr>
                                    <tr>
                                        <td>HaloPesa Float</td>
                                        <td class="text-end">TZS ${numberFormat(sum.halopesa.opening)}</td>
                                        <td class="text-end">TZS ${numberFormat(sum.halopesa.closing)}</td>
                                        <td class="text-end ${sum.halopesa.difference >= 0 ? 'text-success' : 'text-danger'}">${sum.halopesa.difference >= 0 ? '+' : ''}${numberFormat(sum.halopesa.difference)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>`;
                    body.html(html);
                } else {
                    body.html('<div class="alert alert-danger">Failed to load session details.</div>');
                }
            },
            error: function() {
                body.html('<div class="alert alert-danger">Error loading session details.</div>');
            }
        });
    });

    function numberFormat(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
</script>
@endsection
</write_to_file>