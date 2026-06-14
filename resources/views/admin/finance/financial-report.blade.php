@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Financial Report</h5>
        <a href="{{ route('admin.finance.profit-loss') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Profit & Loss</a>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3"><label class="form-label small">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}"></div>
                <div class="col-md-3"><label class="form-label small">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}"></div>
                <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button><a href="{{ route('admin.finance.financial-report') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
            </form>
            <div class="row g-3 text-center">
                <div class="col-md-4"><div class="p-3 bg-success bg-opacity-10 rounded"><h6 class="text-muted">Total Income</h6><h3 class="text-success">TZS {{ number_format($totalIncome, 2) }}</h3></div></div>
                <div class="col-md-4"><div class="p-3 bg-danger bg-opacity-10 rounded"><h6 class="text-muted">Total Expenses</h6><h3 class="text-danger">TZS {{ number_format($totalExpenses, 2) }}</h3></div></div>
                <div class="col-md-4"><div class="p-3 bg-{{ $profit >= 0 ? 'success' : 'danger' }} bg-opacity-10 rounded"><h6 class="text-muted">Net Profit</h6><h3 class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">TZS {{ number_format($profit, 2) }}</h3></div></div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Income by Category</h6></div><div class="card-body p-0">
                @forelse($incomeByCategory as $ic)<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ $ic->category->name ?? 'N/A' }}</span><div><span class="badge bg-secondary me-2">{{ $ic->count }} records</span><strong class="text-success">TZS {{ number_format($ic->total, 2) }}</strong></div></div>@empty<div class="text-muted text-center py-3">No data</div>@endforelse
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Expenses by Category</h6></div><div class="card-body p-0">
                @forelse($expensesByCategory as $ec)<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ ucfirst($ec->category) }}</span><div><span class="badge bg-secondary me-2">{{ $ec->count }} records</span><strong class="text-danger">TZS {{ number_format($ec->total, 2) }}</strong></div></div>@empty<div class="text-muted text-center py-3">No data</div>@endforelse
            </div></div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Income by Staff</h6></div><div class="card-body p-0">
                @if($incomeByStaff->isEmpty())<div class="text-muted text-center py-3">No data</div>
                @else
                <div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Staff</th><th>Records</th><th>Total</th></tr></thead><tbody>@foreach($incomeByStaff as $is)<tr><td>{{ $is->staff->name ?? 'N/A' }}</td><td>{{ $is->count }}</td><td class="text-success fw-semibold">TZS {{ number_format($is->total, 2) }}</td></tr>@endforeach</tbody></table></div>
                @endif
            </div></div>
        </div>
    </div>
</div>
@endsection