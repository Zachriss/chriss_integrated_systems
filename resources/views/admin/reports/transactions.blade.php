@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Transaction Report</h4>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="">All Methods</option>
                <option value="mpesa" {{ $paymentMethod === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                <option value="airtel" {{ $paymentMethod === 'airtel' ? 'selected' : '' }}>Airtel</option>
                <option value="tigo" {{ $paymentMethod === 'tigo' ? 'selected' : '' }}>Tigo</option>
                <option value="halo" {{ $paymentMethod === 'halo' ? 'selected' : '' }}>Halo</option>
                <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Cash</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Income</h6>
                    <h3 class="text-success mb-0">KES {{ number_format($totals['income'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Expenses</h6>
                    <h3 class="text-danger mb-0">KES {{ number_format($totals['expenses'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td><span class="badge bg-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">{{ ucfirst($transaction->type) }}</span></td>
                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}KES {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection