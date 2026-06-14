@extends('super-admin.layouts.super-admin')

@section('title', 'Closing Balance Reports')

@section('content')
<div class="sa-page-header">
    <h1>Closing Balance Reports</h1>
    <p>View daily and monthly closing balance reports.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Today's Closing</p>
            <div class="sa-stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="sa-stat-value" style="color:#166534;">TSh 2,450,000</div>
            <small>Main Operating Account</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Petty Cash</p>
            <div class="sa-stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="sa-stat-value" style="color:#854d0e;">TSh 125,000</div>
            <small>Remaining balance</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Total Cash Flow</p>
            <div class="sa-stat-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="sa-stat-value">TSh 12,575,000</div>
            <small>Net position</small>
        </div>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="font-size:0.95rem;">Closing Report History</h5>
            <div>
                <select class="form-select form-select-sm d-inline-block w-auto">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Year</option>
                </select>
            </div>
        </div>
    </div>
</div>
@endsection