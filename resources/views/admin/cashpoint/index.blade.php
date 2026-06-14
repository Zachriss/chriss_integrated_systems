@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Staff Cash Point Sessions</h4>
        <a href="{{ route('admin.cashpoint.all-sessions') }}" class="btn btn-primary">
            <i class="bi bi-table me-1"></i> All Sessions
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">Total Staff</div>
                    <strong class="fs-4">{{ $staff->count() }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">Open Sessions</div>
                    <strong class="fs-4 text-success">{{ $staff->where('session_status', 'Open')->count() }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">Closed Sessions</div>
                    <strong class="fs-4 text-secondary">{{ $staff->where('session_status', 'Closed')->count() }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">No Session Yet</div>
                    <strong class="fs-4 text-warning">{{ $staff->where('session_status', 'No Session')->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Staff Members</h5>
            <input type="text" id="staffSearch" class="form-control form-control-sm" style="width:250px" placeholder="Search staff...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="staffTable">
                    <thead class="table-light">
                        <tr>
                            <th>Staff Name</th>
                            <th class="text-end">Opening Cash</th>
                            <th class="text-end">Opening Float</th>
                            <th class="text-end">Closing Cash</th>
                            <th class="text-end">Closing Float</th>
                            <th>Session Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $user)
                        @php $s = $user->today_session; @endphp
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td class="text-end">TZS {{ number_format($s ? $s->opening_cash : 0, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($s ? $s->getTotalOpeningFloat() : 0, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($s ? $s->closing_cash : 0, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($s ? $s->getTotalClosingFloat() : 0, 0) }}</td>
                            <td>
                                @if($user->session_status === 'Closed')
                                    <span class="badge bg-success">Closed</span>
                                @elseif($user->session_status === 'Open')
                                    <span class="badge bg-warning text-dark">Open</span>
                                @else
                                    <span class="badge bg-secondary">No Session</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.cashpoint.staff-sessions', $user) }}" class="btn btn-outline-primary" title="View Sessions">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($s && $s->status === 'Closed')
                                    <button class="btn btn-outline-warning reopen-session" data-session-id="{{ $s->id }}" title="Reopen Session">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                    @endif
                                    <button class="btn btn-outline-danger reset-balances" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" title="Reset Balances">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Staff search
    $('#staffSearch').on('keyup', function() {
        const value = this.value.toLowerCase();
        $('#staffTable tbody tr').each(function() {
            $(this).toggle($(this).find('td:first').text().toLowerCase().includes(value));
        });
    });

    // Reopen session
    $('.reopen-session').on('click', function() {
        const sessionId = $(this).data('session-id');
        if (!confirm('Reopen this session? This will clear closing balances.')) return;

        $.ajax({
            url: '{{ url("/admin/cashpoint/sessions") }}/' + sessionId + '/reopen',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Success', text: response.message, timer: 2000 });
                    }
                    setTimeout(() => { location.reload(); }, 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error reopening session';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else { alert(msg); }
            }
        });
    });

    // Reset balances
    $('.reset-balances').on('click', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        if (!confirm(`Reset opening balances for ${userName}?`)) return;

        $.ajax({
            url: '{{ url("/admin/cashpoint/staff") }}/' + userId + '/reset',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Success', text: response.message, timer: 2000 });
                    }
                    setTimeout(() => { location.reload(); }, 1500);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error resetting balances';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else { alert(msg); }
            }
        });
    });
});
</script>
@endsection
</write_to_file>