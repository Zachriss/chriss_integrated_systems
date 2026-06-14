@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Cash Point - {{ $cashPoint->date->format('M d, Y') }}</h4>
        <a href="{{ route('admin.cash-points.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Opening Balances</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6"><span>M-Pesa:</span> <strong>KES {{ number_format($cashPoint->opening_mpesa, 2) }}</strong></div>
                        <div class="col-6"><span>Airtel:</span> <strong>KES {{ number_format($cashPoint->opening_airtel, 2) }}</strong></div>
                        <div class="col-6"><span>Tigo:</span> <strong>KES {{ number_format($cashPoint->opening_tigo, 2) }}</strong></div>
                        <div class="col-6"><span>Halo:</span> <strong>KES {{ number_format($cashPoint->opening_halo, 2) }}</strong></div>
                        <div class="col-12"><span class="text-primary">Total Opening:</span> <strong class="text-primary">KES {{ number_format($cashPoint->total_opening, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Closing Balances</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cash-points.update-closing', $cashPoint->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-2">
                            <div class="col-6"><span>M-Pesa:</span> <input type="number" step="0.01" name="closing_mpesa" value="{{ $cashPoint->closing_mpesa }}" class="form-control form-control-sm" required></div>
                            <div class="col-6"><span>Airtel:</span> <input type="number" step="0.01" name="closing_airtel" value="{{ $cashPoint->closing_airtel }}" class="form-control form-control-sm" required></div>
                            <div class="col-6"><span>Tigo:</span> <input type="number" step="0.01" name="closing_tigo" value="{{ $cashPoint->closing_tigo }}" class="form-control form-control-sm" required></div>
                            <div class="col-6"><span>Halo:</span> <input type="number" step="0.01" name="closing_halo" value="{{ $cashPoint->closing_halo }}" class="form-control form-control-sm" required></div>
                            <div class="col-6"><span>Cash:</span> <input type="number" step="0.01" name="closing_cash" value="{{ $cashPoint->closing_cash }}" class="form-control form-control-sm" required></div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update Closing</button>
                            </div>
                        </div>
                    </form>
                    <hr class="my-2">
                    <div><span class="text-success">Total Closing:</span> <strong class="text-success">KES {{ number_format($cashPoint->total_closing, 2) }}</strong></div>
                    <div><span class="text-muted">Calculated Balance:</span> <strong>KES {{ number_format($cashPoint->calculated_closing, 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transactions</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="bi bi-plus-circle me-1"></i> Add Transaction
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Payment Method</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Time</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td><span class="badge bg-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">{{ ucfirst($transaction->type) }}</span></td>
                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>KES {{ number_format($transaction->amount, 2) }}</td>
                            <td>{{ $transaction->reference ?? '-' }}</td>
                            <td>{{ $transaction->created_at->format('H:i') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.cash-points.destroy-transaction', $transaction->id) }}" class="d-inline" onsubmit="return confirm('Delete this transaction?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.cash-points.add-transaction', $cashPoint->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="mpesa">M-Pesa</option>
                            <option value="airtel">Airtel</option>
                            <option value="tigo">Tigo</option>
                            <option value="halo">Halo</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference (Optional)</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection