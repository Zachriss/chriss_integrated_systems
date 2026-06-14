@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daily Cash Report</h4>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Select Date</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
        </div>
    </form>

    @if($cashPoint)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Cash Point Summary - {{ $cashPoint->date->format('M d, Y') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Opening Balances</h6>
                    <div class="row g-2">
                        <div class="col-6"><span>M-Pesa:</span> <strong>KES {{ number_format($cashPoint->opening_mpesa, 2) }}</strong></div>
                        <div class="col-6"><span>Airtel:</span> <strong>KES {{ number_format($cashPoint->opening_airtel, 2) }}</strong></div>
                        <div class="col-6"><span>Tigo:</span> <strong>KES {{ number_format($cashPoint->opening_tigo, 2) }}</strong></div>
                        <div class="col-6"><span>Halo:</span> <strong>KES {{ number_format($cashPoint->opening_halo, 2) }}</strong></div>
                        <div class="col-12"><span class="text-primary">Total:</span> <strong class="text-primary">KES {{ number_format($cashPoint->total_opening, 2) }}</strong></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Closing Balances</h6>
                    <div class="row g-2">
                        <div class="col-6"><span>M-Pesa:</span> <strong>KES {{ number_format($cashPoint->closing_mpesa, 2) }}</strong></div>
                        <div class="col-6"><span>Airtel:</span> <strong>KES {{ number_format($cashPoint->closing_airtel, 2) }}</strong></div>
                        <div class="col-6"><span>Tigo:</span> <strong>KES {{ number_format($cashPoint->closing_tigo, 2) }}</strong></div>
                        <div class="col-6"><span>Halo:</span> <strong>KES {{ number_format($cashPoint->closing_halo, 2) }}</strong></div>
                        <div class="col-12"><span class="text-success">Total:</span> <strong class="text-success">KES {{ number_format($cashPoint->total_closing, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        No cash point record found for the selected date.
    </div>
    @endif
</div>
@endsection