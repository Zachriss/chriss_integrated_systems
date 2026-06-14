@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Daily Income Report</h5>
        <a href="{{ route('admin.staff-reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('admin.staff-reports.daily-income') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
                <div class="col ms-auto text-end">
                    <strong class="text-success">Grand Total: TZS {{ number_format($totalAmount, 2) }}</strong>
                </div>
            </form>
            @if($dailyIncome->isEmpty())
                <div class="text-center py-4 text-muted">No income data found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Records</th><th>Total Amount</th></tr>
                        </thead>
                        <tbody>
                            @foreach($dailyIncome as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('F d, Y') }}</td>
                                <td>{{ $day->record_count }}</td>
                                <td class="fw-semibold text-success">TZS {{ number_format($day->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $dailyIncome->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection