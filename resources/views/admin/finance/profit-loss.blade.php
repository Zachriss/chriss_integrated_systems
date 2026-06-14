@extends('layouts.chrissDashboardLayout')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Profit & Loss Report</h5>
        <a href="{{ route('admin.finance.financial-report') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text me-1"></i> Financial Report</a>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3"><label class="form-label small">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}"></div>
                <div class="col-md-3"><label class="form-label small">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}"></div>
                <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button><a href="{{ route('admin.finance.profit-loss') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
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
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Income by Service</h6></div><div class="card-body p-0">
                @forelse($perService as $s)<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ $s->service->name ?? 'N/A' }}</span><strong class="text-success">TZS {{ number_format($s->total, 2) }}</strong></div>@empty<div class="text-muted text-center py-3">No data</div>@endforelse
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Expenses by Category</h6></div><div class="card-body p-0">
                @forelse($perExpenseCategory as $e)<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span>{{ ucfirst($e->category) }}</span><strong class="text-danger">TZS {{ number_format($e->total, 2) }}</strong></div>@empty<div class="text-muted text-center py-3">No data</div>@endforelse
            </div></div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm"><div class="card-header bg-transparent"><h6>Daily Trend</h6></div><div class="card-body">
                <div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Income</th><th>Expenses</th><th>Profit</th></tr></thead>
                <tbody>
                    @php $dates = collect(); foreach($dailyIncome as $d){ $dates[$d->date] = ['income'=>$d->total_income,'expense'=>0]; } foreach($dailyExpenses as $d){ if(isset($dates[$d->date])) $dates[$d->date]['expense']=$d->total_expense; else $dates[$d->date]=['income'=>0,'expense'=>$d->total_expense]; } $dates = collect($dates)->sortKeys(); @endphp
                    @foreach($dates as $date => $vals)
                    <tr><td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td><td class="text-success">TZS {{ number_format($vals['income'],2) }}</td><td class="text-danger">TZS {{ number_format($vals['expense'],2) }}</td><td class="{{ ($vals['income']-$vals['expense'])>=0?'text-success':'text-danger' }}">TZS {{ number_format($vals['income']-$vals['expense'],2) }}</td></tr>
                    @endforeach
                </tbody></table></div>
            </div></div>
        </div>
    </div>
</div>
@endsection