@extends('layouts.chrissDashboardLayout')
@section('title', 'End of Day Closing')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $cashPoint->name }} - End of Day Closing</h5>
                    <small class="text-muted">{{ $today->format('d F Y') }}</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{!! session('success') !!}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning">{!! session('warning') !!}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{!! session('error') !!}</div>
                    @endif

                    <!-- Instructions -->
                    <div class="alert alert-info mb-4">
                        <strong>Instructions:</strong> Enter the <u>actual physical closing balance</u> for each provider below.
                        <br><strong>Cash (Treasury)</strong> is shared across all providers - its expected balance reflects ALL deposits minus ALL withdrawals.
                        <br>The system will automatically calculate the expected balance and difference for each provider.
                        <br><small class="text-muted">Only submit when all transactions for today are complete.</small>
                    </div>

                    <form method="POST" action="{{ route('staff.cashpoint.closing.store') }}" id="closingForm">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Provider</th>
                                        <th>Opening Balance</th>
                                        <th>Today's Deposits</th>
                                        <th>Today's Withdrawals</th>
                                        <th>Expected Balance</th>
                                        <th>Actual Closing Balance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($providers as $provider)
                                    @php $data = $providerData[$provider->id] ?? []; @endphp
                                    @php $balance = $data['balance'] ?? []; @endphp
                                    @php $expected = ($balance['opening_balance'] ?? 0) + ($balance['deposits'] ?? 0) - ($balance['withdrawals'] ?? 0); @endphp
                                    @php $isCash = $provider->code === 'CASH'; @endphp
                                    <tr class="{{ $data['is_locked'] ?? false ? 'table-success' : ($isCash ? 'table-warning' : '') }}">
                                        <td>
                                            <strong>{{ $provider->name }}</strong>
                                            <br>
                                            @if($isCash)
                                                <span class="badge bg-warning text-dark">Shared Treasury</span>
                                            @else
                                                <small class="text-muted">{{ $provider->code }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($balance['opening_balance'] ?? 0) }}</td>
                                        <td class="text-end text-success">
                                            {{ number_format($balance['deposits'] ?? 0) }}
                                            @if($isCash)
                                                <br><small class="text-muted">(All providers)</small>
                                            @endif
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ number_format($balance['withdrawals'] ?? 0) }}
                                            @if($isCash)
                                                <br><small class="text-muted">(All providers)</small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold" id="expected_{{ $provider->id }}">
                                            {{ number_format($expected) }}
                                        </td>
                                        <td>
                                            @if($data['is_locked'] ?? false)
                                                <span class="badge bg-success">Locked: {{ number_format($data['closing']->closing_balance ?? 0) }}</span>
                                            @else
                                                <input type="number"
                                                       name="closing_balance[{{ $provider->id }}]"
                                                       class="form-control text-end closing-input"
                                                       min="0"
                                                       step="0.01"
                                                       placeholder="Enter closing balance..."
                                                       data-provider="{{ $provider->id }}"
                                                       data-expected="{{ $expected }}">
                                            @endif
                                        </td>
                                        <td id="status_{{ $provider->id }}">
                                            @if($data['is_locked'] ?? false)
                                                @php $diff = ($data['closing']->closing_balance ?? 0) - $expected; @endphp
                                                <span class="badge bg-{{ abs($diff) < 0.01 ? 'success' : 'warning' }}">
                                                    Diff: {{ number_format($diff) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('staff.cashpoint.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg" id="submitClosingBtn" data-no-spinner>
                                <i class="bi bi-lock"></i> Submit & Lock All Providers
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Calculate difference in real-time when user types closing balance
document.querySelectorAll('.closing-input').forEach(function(input) {
    input.addEventListener('input', function() {
        const providerId = this.dataset.provider;
        const expected = parseFloat(this.dataset.expected) || 0;
        const actual = parseFloat(this.value) || 0;
        const diff = actual - expected;
        const statusCell = document.getElementById('status_' + providerId);
        
        if (this.value === '') {
            statusCell.innerHTML = '<span class="text-muted">Pending</span>';
        } else if (Math.abs(diff) < 0.01) {
            statusCell.innerHTML = '<span class="badge bg-success">✓ Match (Diff: 0)</span>';
        } else if (diff > 0) {
            statusCell.innerHTML = '<span class="badge bg-warning">Surplus: ' + diff.toLocaleString() + '</span>';
        } else {
            statusCell.innerHTML = '<span class="badge bg-danger">Deficit: ' + diff.toLocaleString() + '</span>';
        }
    });
});

// Confirm before submitting
document.getElementById('closingForm').addEventListener('submit', function(e) {
    const hasInputs = document.querySelectorAll('.closing-input:not([disabled])').length > 0;
    
    if (hasInputs > 0 && !confirm('Are you sure you want to close the day? This will lock all provider balances and cannot be undone without Super Admin access.')) {
        e.preventDefault();
    }
});

// Disable button on submit
document.getElementById('submitClosingBtn').addEventListener('click', function() {
    setTimeout(() => {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    }, 100);
});
</script>
@endpush