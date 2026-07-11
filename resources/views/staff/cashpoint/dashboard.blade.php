@extends('layouts.chrissDashboardLayout')
@section('title', 'Cash Point Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $cashPoint->name }} - Today's Summary</h5>
                    <small class="text-muted">{{ $today->format('d F Y') }}</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <div class="row">
                        @foreach($providers as $provider)
                        @php $balance = $balances[$provider->id] ?? []; @endphp
                        @php $isCash = $provider->code === 'CASH'; @endphp
                        <div class="col-md-6 mb-3">
                            <div class="card border-{{ $isCash ? 'warning' : ($provider->code === 'MPESA' ? 'success' : 'primary') }}">
                                <div class="card-header d-flex justify-content-between">
                                    <strong>{{ $provider->name }}</strong>
                                    @if($isCash)
                                        <span class="badge bg-warning text-dark">Shared Treasury</span>
                                    @else
                                        <span class="badge bg-info">{{ ucfirst($provider->code) }}</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <small class="d-block text-muted">Opening</small>
                                            <strong>{{ number_format($balance['opening_balance'] ?? 0) }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="d-block text-muted">Deposits</small>
                                            <strong class="text-success">{{ number_format($balance['deposits'] ?? 0) }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="d-block text-muted">Withdrawals</small>
                                            <strong class="text-danger">{{ number_format($balance['withdrawals'] ?? 0) }}</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span><strong>Current Balance:</strong></span>
                                        <span class="badge bg-primary fs-6">{{ number_format($balance['current_balance'] ?? 0) }} TZS</span>
                                    </div>

                                    @if($balance['closing'] && $balance['closing']['is_locked'])
                                    <div class="alert alert-success mb-0 py-2">
                                        <small>✓ Closed - Expected: {{ number_format($balance['closing']['expected']) }} | Diff: {{ number_format($balance['closing']['difference']) }}</small>
                                    </div>
                                    @elseif($balance['closing'] && !$balance['closing']['is_locked'])
                                    <div class="alert alert-warning mb-0 py-2">
                                        <small>Closing recorded, not locked yet</small>
                                    </div>
                                    @else
                                    <div class="alert alert-light mb-0 py-2">
                                        <small>No closing recorded yet</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End of Day Closing -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-lock-fill"></i> End of Day Operations</h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">When all transactions for today are complete, submit the closing balances for all providers at once.</p>
                    <a href="{{ route('staff.cashpoint.closing.create') }}" class="btn btn-warning btn-lg">
                        <i class="bi bi-lock"></i> End of Day Closing
                    </a>
                    <small class="d-block text-muted mt-2">Enter physical closing balances for each provider. System will calculate expected vs actual.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection