@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-table me-2"></i>All Cash Point Sessions</h4>
        <a href="{{ route('super-admin.cashpoint.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" id="filter_from" name="from">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" id="filter_to" name="to">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filter_status">
                        <option value="">All</option>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Staff Member</label>
                    <select class="form-select" id="filter_user">
                        <option value="">All Staff</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                        <i class="bi bi-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="sessionsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Staff</th>
                            <th class="text-end">Opening Cash</th>
                            <th class="text-end">Closing Cash</th>
                            <th class="text-end">Cash Diff</th>
                            <th class="text-end">Total Float</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sessionsTableBody">
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-arrow-up me-1"></i> Apply filters to load data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Session Detail Modal --}}
<div class="modal fade" id="sessionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Session Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sessionDetailBody">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- Correction Modal --}}
<div class="modal fade" id="correctionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Correct Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="correctionForm">
                    @csrf
                    <input type="hidden" id="correction_session_id" name="session_id">
                    <div class="mb-3">
                        <label class="form-label">Field to Correct</label>
                        <select class="form-select" id="correction_field" name="field" required>
                            <option value="">Select field...</option>
                            <option value="opening_cash">Opening Cash</option>
                            <option value="opening_mpesa_float">Opening M-Pesa Float</option>
                            <option value="opening_airtel_float">Opening Airtel Float</option>
                            <option value="opening_mixx_float">Opening Mixx Float</option>
                            <option value="opening_halopesa_float">Opening HaloPesa Float</option>
                            <option value="closing_cash">Closing Cash</option>
                            <option value="closing_mpesa_float">Closing M-Pesa Float</option>
                            <option value="closing_airtel_float">Closing Airtel Float</option>
                            <option value="closing_mixx_float">Closing Mixx Float</option>
                            <option value="closing_halopesa_float">Closing HaloPesa Float</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Value (TZS)</label>
                        <input type="number" class="form-control" id="correction_value" name="value" min="0" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="applyCorrectionBtn">
                    <i class="bi bi-check-circle me-1"></i> Apply Correction
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function loadSessions() {
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();
        const status = $('#filter_status').val();
        const userId = $('#filter_user').val();

        const body = $('#sessionsTableBody');
        body.html('<tr><td colspan="8" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading...</td></tr>');

        $.ajax({
            url: '{{ route("super-admin.cashpoint.sessions.data") }}',
            method: 'GET',
            data: { from, to, status, user_id: userId },
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                const data = response.data;
                if (data.length === 0) {
                    body.html('<tr><td colspan="8" class="text-center text-muted py-4">No sessions found.</td></tr>');
                    return;
                }
                let html = '';
                data.forEach(function(s) {
                    const totalFloat = parseFloat(s.opening_mpesa_float) + parseFloat(s.opening_airtel_float) + parseFloat(s.opening_mixx_float) + parseFloat(s.opening_halopesa_float);
                    html += `<tr>
                        <td>${s.session_date}</td>
                        <td><strong>${s.user?.name || 'Unknown'}</strong></td>
                        <td class="text-end">TZS ${nf(s.opening_cash)}</td>
                        <td class="text-end">TZS ${nf(s.closing_cash)}</td>
                        <td class="text-end ${parseFloat(s.cash_difference) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(s.cash_difference) >= 0 ? '+' : ''}${nf(s.cash_difference)}</td>
                        <td class="text-end">TZS ${nf(totalFloat)}</td>
                        <td><span class="badge bg-${s.status === 'Closed' ? 'success' : 'warning text-dark'}">${s.status}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary view-session" data-id="${s.id}" title="View"><i class="bi bi-eye"></i></button>
                                ${s.status === 'Closed' ? `<button class="btn btn-outline-warning reopen-session" data-id="${s.id}" title="Reopen"><i class="bi bi-unlock"></i></button>` : ''}
                                <button class="btn btn-outline-danger correct-session" data-id="${s.id}" title="Correct"><i class="bi bi-pencil"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                body.html(html);
            },
            error: function() {
                body.html('<tr><td colspan="8" class="text-center text-danger py-4">Error loading sessions.</td></tr>');
            }
        });
    }

    $('#applyFiltersBtn').on('click', loadSessions);
    $('#clearFiltersBtn').on('click', function() {
        $('#filterForm')[0].reset();
        loadSessions();
    });

    // Auto-load on page load
    loadSessions();

    // View session
    $(document).on('click', '.view-session', function() {
        const id = $(this).data('id');
        const body = $('#sessionDetailBody');
        body.html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
        $('#sessionDetailModal').modal('show');

        $.ajax({
            url: '{{ url("/super-admin/cashpoint/sessions") }}/' + id,
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    const s = res.session, sum = res.summary;
                    body.html(`
                        <div class="mb-3"><strong>Date:</strong> ${s.session_date} | <strong>Staff:</strong> ${s.user?.name} | <strong>Status:</strong> <span class="badge bg-${s.status==='Closed'?'success':'warning'}">${s.status}</span></div>
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

    // Reopen session
    $(document).on('click', '.reopen-session', function() {
        if (!confirm('Reopen this session?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '{{ url("/super-admin/cashpoint/sessions") }}/' + id + '/reopen',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                if (res.success) {
                    if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'success', title: 'Success', text: res.message, timer: 2000 });
                    loadSessions();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error';
                if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
            }
        });
    });

    // Correction modal
    $(document).on('click', '.correct-session', function() {
        const id = $(this).data('id');
        $('#correction_session_id').val(id);
        $('#correction_field').val('');
        $('#correction_value').val('');
        $('#correctionModal').modal('show');
    });

    $('#applyCorrectionBtn').on('click', function() {
        const id = $('#correction_session_id').val();
        const field = $('#correction_field').val();
        const value = $('#correction_value').val();
        if (!field || !value) {
            alert('Please select a field and enter a value.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Applying...');

        $.ajax({
            url: '{{ url("/super-admin/cashpoint/sessions") }}/' + id + '/correct',
            method: 'POST',
            data: { field, value, _token: csrfToken },
            success: function(res) {
                if (res.success) {
                    $('#correctionModal').modal('hide');
                    if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'success', title: 'Corrected', text: res.message, timer: 2000 });
                    loadSessions();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error';
                if (typeof showSystemAlert === 'function') showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Apply Correction');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Apply Correction');
            }
        });
    });

    function nf(n) {
        const num = parseFloat(n) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
</script>
@endsection
</write_to_file>