@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>{{ $staff->name }} - Sessions</h4>
        <a href="{{ route('admin.cashpoint.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Back
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
                            <th class="text-end">Total Float Open</th>
                            <th class="text-end">Total Float Close</th>
                            <th>Status</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
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
                            <td class="text-end">TZS {{ number_format($s->getTotalOpeningFloat(), 0) }}</td>
                            <td class="text-end">TZS {{ number_format($s->getTotalClosingFloat(), 0) }}</td>
                            <td>
                                @if($s->status === 'Closed')
                                    <span class="badge bg-success">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Open</span>
                                @endif
                            </td>
                            <td>{{ $s->opened_at ? $s->opened_at->format('H:i') : '-' }}</td>
                            <td>{{ $s->closed_at ? $s->closed_at->format('H:i') : '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-session" data-session-id="{{ $s->id }}" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($s->status === 'Closed')
                                <button class="btn btn-sm btn-outline-warning reopen-session" data-session-id="{{ $s->id }}" title="Reopen">
                                    <i class="bi bi-unlock"></i>
                                </button>
                                @endif
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
    <div class="mt-3">{{ $sessions->links() }}</div>
    @endif
</div>

{{-- Session Detail Modal --}}
<div class="modal fade" id="sessionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Session Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sessionDetailBody">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
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
        const id = $(this).data('session-id');
        const body = $('#sessionDetailBody');
        body.html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
        $('#sessionDetailModal').modal('show');

        $.ajax({
            url: '{{ url("/admin/cashpoint/sessions") }}/' + id,
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    const s = res.session, sum = res.summary;
                    body.html(`
                        <div class="mb-3"><strong>Date:</strong> ${s.session_date} | <strong>Status:</strong> <span class="badge bg-${s.status==='Closed'?'success':'warning'}">${s.status}</span></div>
                        <table class="table table-bordered">
                            <thead class="table-light"><tr><th>Item</th><th class="text-end">Opening</th><th class="text-end">Closing</th><th class="text-end">Difference</th></tr></thead>
                            <tbody>
                                <tr><td><strong>Cash Drawer</strong></td><td class="text-end">TZS ${nf(sum.cash.opening)}</td><td class="text-end">TZS ${nf(sum.cash.closing)}</td><td class="text-end ${sum.cash.difference>=0?'text-success':'text-danger'}">${sum.cash.difference>=0?'+':''}${nf(sum.cash.difference)}</td></tr>
                                <tr><td>M-Pesa</td><td class="text-end">TZS ${nf(sum.mpesa.opening)}</td><td class="text-end">TZS ${nf(sum.mpesa.closing)}</td><td class="text-end ${sum.mpesa.difference>=0?'text-success':'text-danger'}">${sum.mpesa.difference>=0?'+':''}${nf(sum.mpesa.difference)}</td></tr>
                                <tr><td>Airtel Money</td><td class="text-end">TZS ${nf(sum.airtel.opening)}</td><td class="text-end">TZS ${nf(sum.airtel.closing)}</td><td class="text-end ${sum.airtel.difference>=0?'text-success':'text-danger'}">${sum.airtel.difference>=0?'+':''}${nf(sum.airtel.difference)}</td></tr>
                                <tr><td>Mixx by Yas</td><td class="text-end">TZS ${nf(sum.mixx.opening)}</td><td class="text-end">TZS ${nf(sum.mixx.closing)}</td><td class="text-end ${sum.mixx.difference>=0?'text-success':'text-danger'}">${sum.mixx.difference>=0?'+':''}${nf(sum.mixx.difference)}</td></tr>
                                <tr><td>HaloPesa</td><td class="text-end">TZS ${nf(sum.halopesa.opening)}</td><td class="text-end">TZS ${nf(sum.halopesa.closing)}</td><td class="text-end ${sum.halopesa.difference>=0?'text-success':'text-danger'}">${sum.halopesa.difference>=0?'+':''}${nf(sum.halopesa.difference)}</td></tr>
                            </tbody>
                        </table>
                    `);
                }
            },
            error: () => body.html('<div class="alert alert-danger">Error loading details.</div>')
        });
    });

    $('.reopen-session').on('click', function() {
        if (!confirm('Reopen this session?')) return;
        const id = $(this).data('session-id');
        $.ajax({
            url: '{{ url("/admin/cashpoint/sessions") }}/' + id + '/reopen',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'success', title: 'Success', text: res.message, timer: 2000 });
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error';
                if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
            }
        });
    });

    function nf(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
});
</script>
@endsection
</write_to_file>