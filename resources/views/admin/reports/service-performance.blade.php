@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Service Performance Report</h4>
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
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Service Performance Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Service</th>
                            <th>Category</th>
                            <th>Total Requests</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th class="text-end">Total Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performanceData as $data)
                        <tr>
                            <td>{{ $data['service'] }}</td>
                            <td><span class="badge bg-secondary">{{ $data['category'] }}</span></td>
                            <td>{{ $data['total_requests'] }}</td>
                            <td><span class="badge bg-success">{{ $data['completed'] }}</span></td>
                            <td><span class="badge bg-warning">{{ $data['pending'] }}</span></td>
                            <td class="text-end text-success"><strong>KES {{ number_format($data['total_income'], 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection