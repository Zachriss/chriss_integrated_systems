@extends('super-admin.layouts.super-admin')

@section('title', 'Clear Cache')

@section('content')
<div class="sa-page-header">
    <h1>Clear Cache</h1>
    <p>Clear system caches and temporary data.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body text-center py-5">
                <i class="bi bi-arrow-clockwise" style="font-size:2.5rem;color:#637cf4;"></i>
                <h5 class="fw-bold mt-3 mb-2">Clear All Caches</h5>
                <p class="text-muted small mb-3">Route, config, view, cache, and compiled files.</p>
                <form method="POST" action="{{ route('super-admin.maintenance.clear-cache') }}">
                    @csrf
                    <button type="submit" class="btn btn-sa-primary"><i class="bi bi-trash me-2"></i> Clear All Cache</button>
                </form>
                @if(session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body py-5">
                <h5 class="fw-bold mb-3">Cache Details</h5>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>Route Cache</span>
                    <span class="text-muted">Cleared on clear</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>Config Cache</span>
                    <span class="text-muted">Cleared on clear</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>View Cache</span>
                    <span class="text-muted">Cleared on clear</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span>Application Cache</span>
                    <span class="text-muted">Cleared on clear</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection