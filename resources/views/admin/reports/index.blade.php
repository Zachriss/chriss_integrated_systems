@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <h4 class="mb-4">Reports</h4>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cash-stack text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Daily Cash Report</h5>
                    <p class="text-muted mb-3">View daily cash point summaries and balances.</p>
                    <a href="{{ route('admin.reports.daily-cash') }}" class="btn btn-primary">
                        <i class="bi bi-eye me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-arrow-left-right text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Transaction Report</h5>
                    <p class="text-muted mb-3">View all income and expense transactions.</p>
                    <a href="{{ route('admin.reports.transactions') }}" class="btn btn-success">
                        <i class="bi bi-eye me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-graph-up text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Service Performance</h5>
                    <p class="text-muted mb-3">Analyze service income and request volumes.</p>
                    <a href="{{ route('admin.reports.service-performance') }}" class="btn btn-warning">
                        <i class="bi bi-eye me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection