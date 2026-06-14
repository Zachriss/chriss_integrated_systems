@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container py-4">
    <h5 class="mb-4">Staff Income Reports</h5>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('admin.staff-reports.daily-income') }}" class="card border-0 shadow-sm text-decoration-none text-reset h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-calendar-check display-4 text-primary"></i>
                    <h5 class="mt-2">Daily Income</h5>
                    <p class="text-muted small">Aggregated income by date</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.staff-reports.per-staff') }}" class="card border-0 shadow-sm text-decoration-none text-reset h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-person-badge display-4 text-success"></i>
                    <h5 class="mt-2">Per Staff</h5>
                    <p class="text-muted small">Income breakdown per staff member</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.staff-reports.per-service') }}" class="card border-0 shadow-sm text-decoration-none text-reset h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-gear display-4 text-warning"></i>
                    <h5 class="mt-2">Per Service</h5>
                    <p class="text-muted small">Income breakdown per service</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.staff-reports.per-category') }}" class="card border-0 shadow-sm text-decoration-none text-reset h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-grid display-4 text-info"></i>
                    <h5 class="mt-2">Per Category</h5>
                    <p class="text-muted small">Income breakdown per category</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection