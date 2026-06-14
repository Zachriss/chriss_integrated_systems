@extends('super-admin.layouts.super-admin')

@section('title', 'Stock Reports')

@section('content')
<div class="sa-page-header">
    <h1>Stock Reports</h1>
    <p>View inventory reports and analytics.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Total Products</p>
            <div class="sa-stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="sa-stat-value">{{ App\Models\Product::count() }}</div>
            <small>registered items</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Low Stock Items</p>
            <div class="sa-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="sa-stat-value" style="color:#dc2626;">{{ App\Models\Product::where('stock', '<=', 5)->count() }}</div>
            <small>needs reorder</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Total Stock Value</p>
            <div class="sa-stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="sa-stat-value">TSh {{ number_format(App\Models\Product::sum(\DB::raw('stock * price')), 2) }}</div>
            <small>current valuation</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sa-card sa-stat">
            <p class="sa-stat-label">Categories</p>
            <div class="sa-stat-icon"><i class="bi bi-tags"></i></div>
            <div class="sa-stat-value">{{ App\Models\Product::distinct('category')->count('category') }}</div>
            <small>product categories</small>
        </div>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="font-size:0.95rem;">Stock Movement History</h5>
            <div>
                <select class="form-select form-select-sm d-inline-block w-auto">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>This Quarter</option>
                </select>
            </div>
        </div>
        <p class="text-muted small">Stock movement tracking will appear here once transactions are recorded.</p>
    </div>
</div>
@endsection