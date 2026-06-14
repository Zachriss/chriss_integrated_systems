@extends('super-admin.layouts.super-admin')

@section('title', 'Transaction Categories')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Transaction Categories</h1>
        <p>Manage transaction categories for cash points.</p>
    </div>
    <button class="btn btn-sa-primary"><i class="bi bi-plus-lg me-1"></i> New Category</button>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Income Categories</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-cash text-success me-2"></i> Sales Revenue</span>
                        <span class="sa-badge sa-badge-active">12 transactions</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-currency-dollar text-success me-2"></i> Service Fees</span>
                        <span class="sa-badge sa-badge-active">8 transactions</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-bank text-success me-2"></i> Bank Deposits</span>
                        <span class="sa-badge sa-badge-active">5 transactions</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body">
                <h5 class="fw-bold mb-3" style="font-size:0.95rem;">Expense Categories</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-cart text-danger me-2"></i> Supplies & Materials</span>
                        <span class="sa-badge" style="background:#fef2f2;color:#991b1b;">15 transactions</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-building text-danger me-2"></i> Rent & Utilities</span>
                        <span class="sa-badge" style="background:#fef2f2;color:#991b1b;">3 transactions</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-truck text-danger me-2"></i> Transport</span>
                        <span class="sa-badge" style="background:#fef2f2;color:#991b1b;">7 transactions</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection