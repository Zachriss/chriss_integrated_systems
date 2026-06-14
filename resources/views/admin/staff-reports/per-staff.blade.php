@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Income Per Staff</h5>
        <a href="{{ route('admin.staff-reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Staff</label>
                    <select name="staff_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($staffMembers as $s)
                            <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('admin.staff-reports.per-staff') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
            @if($perStaff->isEmpty())
                <div class="text-center py-4 text-muted">No data found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Staff</th><th>Records</th><th>Total</th><th></th></tr></thead>
                        <tbody>
                            @foreach($perStaff as $row)
                            <tr>
                                <td>{{ $row->staff->name ?? 'N/A' }}</td>
                                <td>{{ $row->record_count }}</td>
                                <td class="fw-semibold text-success">TZS {{ number_format($row->total_amount, 2) }}</td>
                                <td><a href="{{ route('admin.staff-reports.staff-detail', $row->staff_id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Detail</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $perStaff->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection