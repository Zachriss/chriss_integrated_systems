@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Cash Point</h4>
        <div>
            @if($session && $session->status === 'Open' && !$needsSetup)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#closeSessionModal">
                    <i class="bi bi-lock-fill me-1"></i> Close Session
                </button>
            @endif
            <a href="{{ route('staff.cashpoint.history') }}" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-clock-history me-1"></i> History
            </a>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Session Status</div>
                    @if(!$session || $needsSetup)
                        <span class="badge bg-warning text-dark fs-6">Not Started</span>
                    @elseif($session->status === 'Open')
                        <span class="badge bg-success fs-6">Open</span>
                    @else
                        <span class="badge bg-secondary fs-6">Closed</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Date</div>
                    <strong>{{ now()->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Today's Cash</div>
                    <strong class="fs-5">TZS {{ number_format($session ? $session->opening_cash : 0, 0) }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Total Float</div>
                    <strong class="fs-5">TZS {{ number_format($session ? $session->getTotalOpeningFloat() : 0, 0) }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Provider Balance Cards --}}
    @if($session && !$needsSetup)
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"><i class="bi bi-cash-coin me-1"></i> Cash Drawer</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Opening</small>
                        <strong>TZS {{ number_format($session->opening_cash, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Closing</small>
                        <strong>TZS {{ number_format($session->closing_cash, 0) }}</strong>
                    </div>
                    @if($session->status === 'Closed')
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Difference</small>
                        <strong class="text-{{ $session->cash_difference >= 0 ? 'success' : 'danger' }}">
                            {{ $session->cash_difference >= 0 ? '+' : '' }}{{ number_format($session->cash_difference, 0) }}
                        </strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"><i class="bi bi-phone me-1"></i> M-Pesa</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Float</small>
                        <strong>TZS {{ number_format($session->opening_mpesa_float, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Closing</small>
                        <strong>TZS {{ number_format($session->closing_mpesa_float, 0) }}</strong>
                    </div>
                    @if($session->status === 'Closed')
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Difference</small>
                        <strong class="text-{{ $session->mpesa_difference >= 0 ? 'success' : 'danger' }}">
                            {{ $session->mpesa_difference >= 0 ? '+' : '' }}{{ number_format($session->mpesa_difference, 0) }}
                        </strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"><i class="bi bi-phone me-1"></i> Airtel Money</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Float</small>
                        <strong>TZS {{ number_format($session->opening_airtel_float, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Closing</small>
                        <strong>TZS {{ number_format($session->closing_airtel_float, 0) }}</strong>
                    </div>
                    @if($session->status === 'Closed')
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Difference</small>
                        <strong class="text-{{ $session->airtel_difference >= 0 ? 'success' : 'danger' }}">
                            {{ $session->airtel_difference >= 0 ? '+' : '' }}{{ number_format($session->airtel_difference, 0) }}
                        </strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"><i class="bi bi-phone me-1"></i> Mixx by Yas</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Float</small>
                        <strong>TZS {{ number_format($session->opening_mixx_float, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Closing</small>
                        <strong>TZS {{ number_format($session->closing_mixx_float, 0) }}</strong>
                    </div>
                    @if($session->status === 'Closed')
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Difference</small>
                        <strong class="text-{{ $session->mixx_difference >= 0 ? 'success' : 'danger' }}">
                            {{ $session->mixx_difference >= 0 ? '+' : '' }}{{ number_format($session->mixx_difference, 0) }}
                        </strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2"><i class="bi bi-phone me-1"></i> HaloPesa</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Float</small>
                        <strong>TZS {{ number_format($session->opening_halopesa_float, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Closing</small>
                        <strong>TZS {{ number_format($session->closing_halopesa_float, 0) }}</strong>
                    </div>
                    @if($session->status === 'Closed')
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Difference</small>
                        <strong class="text-{{ $session->halopesa_difference >= 0 ? 'success' : 'danger' }}">
                            {{ $session->halopesa_difference >= 0 ? '+' : '' }}{{ number_format($session->halopesa_difference, 0) }}
                        </strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Daily Summary Table --}}
    @if($summary)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Daily Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
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
                            <td class="text-end">TZS {{ number_format($summary['cash']['opening'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($summary['cash']['closing'], 0) }}</td>
                            <td class="text-end {{ $summary['cash']['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $summary['cash']['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['cash']['difference'], 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>M-Pesa Float</td>
                            <td class="text-end">TZS {{ number_format($summary['mpesa']['opening'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($summary['mpesa']['closing'], 0) }}</td>
                            <td class="text-end {{ $summary['mpesa']['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $summary['mpesa']['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['mpesa']['difference'], 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Airtel Money Float</td>
                            <td class="text-end">TZS {{ number_format($summary['airtel']['opening'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($summary['airtel']['closing'], 0) }}</td>
                            <td class="text-end {{ $summary['airtel']['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $summary['airtel']['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['airtel']['difference'], 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Mixx by Yas Float</td>
                            <td class="text-end">TZS {{ number_format($summary['mixx']['opening'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($summary['mixx']['closing'], 0) }}</td>
                            <td class="text-end {{ $summary['mixx']['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $summary['mixx']['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['mixx']['difference'], 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>HaloPesa Float</td>
                            <td class="text-end">TZS {{ number_format($summary['halopesa']['opening'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($summary['halopesa']['closing'], 0) }}</td>
                            <td class="text-end {{ $summary['halopesa']['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $summary['halopesa']['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['halopesa']['difference'], 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Sessions --}}
    @if($recentSessions->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Sessions (30 Days)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Opening Cash</th>
                            <th class="text-end">Closing Cash</th>
                            <th class="text-end">Cash Diff</th>
                            <th class="text-end">Total Float In/Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $rs)
                        @php
                            $totalFloatDiff = $rs->mpesa_difference + $rs->airtel_difference + $rs->mixx_difference + $rs->halopesa_difference;
                        @endphp
                        <tr>
                            <td>{{ $rs->session_date->format('M d, Y') }}</td>
                            <td class="text-end">TZS {{ number_format($rs->opening_cash, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($rs->closing_cash, 0) }}</td>
                            <td class="text-end {{ $rs->cash_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $rs->cash_difference >= 0 ? '+' : '' }}{{ number_format($rs->cash_difference, 0) }}
                            </td>
                            <td class="text-end {{ $totalFloatDiff >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalFloatDiff >= 0 ? '+' : '' }}{{ number_format($totalFloatDiff, 0) }}
                            </td>
                            <td>
                                @if($rs->status === 'Closed')
                                    <span class="badge bg-success">Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Open</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Opening Balance Modal - shown on first time or reset --}}
@if($needsSetup)
<div class="overlay-container" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1050; display: flex; align-items: center; justify-content: center;">
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: -1;"></div>
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin: 0; width: 90%; max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Set Opening Balances</h5>
            </div>
            <div class="modal-body">
                @if(!$session || !$session->hasOpeningBalances())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Welcome! Please enter your opening balances for the day.
                    @if($session && ($session->opening_cash > 0 || $session->getTotalOpeningFloat() > 0))
                        <br>Values have been carried forward from yesterday. Adjust if needed.
                    @endif
                </div>
                @endif
                <form id="openingBalanceForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-cash-coin me-1"></i>Cash Drawer</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="opening_cash" name="opening_cash"
                                            value="{{ $session ? $session->opening_cash : 0 }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>M-Pesa Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="opening_mpesa_float" name="opening_mpesa_float"
                                            value="{{ $session ? $session->opening_mpesa_float : 0 }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Airtel Money Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="opening_airtel_float" name="opening_airtel_float"
                                            value="{{ $session ? $session->opening_airtel_float : 0 }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Mixx by Yas Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="opening_mixx_float" name="opening_mixx_float"
                                            value="{{ $session ? $session->opening_mixx_float : 0 }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>HaloPesa Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="opening_halopesa_float" name="opening_halopesa_float"
                                            value="{{ $session ? $session->opening_halopesa_float : 0 }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-lg w-100" id="saveOpeningBtn">
                    <i class="bi bi-check-circle me-2"></i> Start Day - Save Opening Balances
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Close Session Modal --}}
@if($session && $session->status === 'Open' && !$needsSetup)
<div class="modal fade" id="closeSessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-lock-fill me-2"></i>Close Daily Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Enter the remaining balances for cash drawer and each provider.
                </div>
                <form id="closeSessionForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-cash-coin me-1"></i>Remaining Cash Drawer</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="closing_cash" name="closing_cash"
                                            value="{{ $session->closing_cash ?: 0 }}" min="0" step="0.01" required>
                                    </div>
                                    <small class="text-muted">Opening: TZS {{ number_format($session->opening_cash, 0) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Remaining M-Pesa Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="closing_mpesa_float" name="closing_mpesa_float"
                                            value="{{ $session->closing_mpesa_float ?: 0 }}" min="0" step="0.01" required>
                                    </div>
                                    <small class="text-muted">Opening: TZS {{ number_format($session->opening_mpesa_float, 0) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Remaining Airtel Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="closing_airtel_float" name="closing_airtel_float"
                                            value="{{ $session->closing_airtel_float ?: 0 }}" min="0" step="0.01" required>
                                    </div>
                                    <small class="text-muted">Opening: TZS {{ number_format($session->opening_airtel_float, 0) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Remaining Mixx Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="closing_mixx_float" name="closing_mixx_float"
                                            value="{{ $session->closing_mixx_float ?: 0 }}" min="0" step="0.01" required>
                                    </div>
                                    <small class="text-muted">Opening: TZS {{ number_format($session->opening_mixx_float, 0) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-phone me-1"></i>Remaining HaloPesa Float</h6>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control form-control-lg" id="closing_halopesa_float" name="closing_halopesa_float"
                                            value="{{ $session->closing_halopesa_float ?: 0 }}" min="0" step="0.01" required>
                                    </div>
                                    <small class="text-muted">Opening: TZS {{ number_format($session->opening_halopesa_float, 0) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-lg" id="closeSessionBtn">
                    <i class="bi bi-check-circle me-2"></i> Close & Calculate
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Save Opening Balances - using vanilla JS with Fetch API
    const saveOpeningBtn = document.getElementById('saveOpeningBtn');
    if (saveOpeningBtn) {
        saveOpeningBtn.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            const data = {
                opening_cash: document.getElementById('opening_cash').value,
                opening_mpesa_float: document.getElementById('opening_mpesa_float').value,
                opening_airtel_float: document.getElementById('opening_airtel_float').value,
                opening_mixx_float: document.getElementById('opening_mixx_float').value,
                opening_halopesa_float: document.getElementById('opening_halopesa_float').value
            };

            fetch('{{ route("staff.cashpoint.opening.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(response => {
                if (response.success) {
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Success', text: response.message, timer: 2000 });
                    }
                    setTimeout(() => { location.reload(); }, 1500);
                }
            })
            .catch(function(xhr) {
                const msg = 'Error saving opening balances';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else {
                    alert(msg);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Start Day - Save Opening Balances';
            });
        });
    }

    // Close Session
    const closeSessionBtn = document.getElementById('closeSessionBtn');
    if (closeSessionBtn) {
        closeSessionBtn.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Closing...';

            const data = {
                closing_cash: document.getElementById('closing_cash').value,
                closing_mpesa_float: document.getElementById('closing_mpesa_float').value,
                closing_airtel_float: document.getElementById('closing_airtel_float').value,
                closing_mixx_float: document.getElementById('closing_mixx_float').value,
                closing_halopesa_float: document.getElementById('closing_halopesa_float').value
            };

            fetch('{{ route("staff.cashpoint.closing.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(response => {
                if (response.success) {
                    // Hide close session modal using Bootstrap
                    const closeModal = bootstrap.Modal.getInstance(document.getElementById('closeSessionModal'));
                    if (closeModal) closeModal.hide();
                    if (typeof showSystemAlert === 'function') {
                        showSystemAlert({ theme: 'success', title: 'Session Closed!', text: response.message, timer: 2000 });
                    }
                    setTimeout(() => { location.reload(); }, 1500);
                }
            })
            .catch(function(xhr) {
                const msg = 'Error closing session';
                if (typeof showSystemAlert === 'function') {
                    showSystemAlert({ theme: 'danger', title: 'Error', text: msg });
                } else {
                    alert(msg);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Close & Calculate';
            });
        });
    }
});
</script>
@endsection
</write_to_file>