@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>{{ $staff->name }} - Income Detail</h5>
        <a href="{{ route('admin.staff-reports.per-staff') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3"><label class="form-label small">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
                <div class="col-md-3"><label class="form-label small">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
                <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button><a href="{{ route('admin.staff-reports.staff-detail', $staff) }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
                <div class="col ms-auto text-end"><strong class="text-success">Total: TZS {{ number_format($totalAmount, 2) }}</strong></div>
            </form>
            @if($records->isEmpty())<div class="text-center py-4 text-muted">No records found.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Service</th><th>Category</th><th>Task</th><th>Qty</th><th>Amount</th><th>Notes</th></tr></thead>
                    <tbody>
                        @foreach($records as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</td>
                            <td>{{ $r->service->name ?? 'N/A' }}</td>
                            <td>{{ $r->category->name ?? 'N/A' }}</td>
                            <td>{{ $r->task->title ?? '—' }}</td>
                            <td>{{ $r->quantity }}</td>
                            <td class="fw-semibold text-success">TZS {{ number_format($r->amount, 2) }}</td>
                            <td><small>{{ $r->description }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $records->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection