@extends('super-admin.layouts.super-admin')

@section('title', 'Optimize System')

@section('content')
<div class="sa-page-header">
    <h1>Optimize System</h1>
    <p>Optimize the application for better performance.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body text-center py-5">
                <i class="bi bi-gear-wide-connected" style="font-size:2.5rem;color:#637cf4;"></i>
                <h5 class="fw-bold mt-3 mb-2">Optimize Application</h5>
                <p class="text-muted small mb-3">Cache routes, config, events, and views for production.</p>
                <form method="POST" action="{{ route('super-admin.maintenance.optimize') }}">
                    @csrf
                    <button type="submit" class="btn btn-sa-primary"><i class="bi bi-gear me-2"></i> Optimize Now</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body py-5">
                <h5 class="fw-bold mb-3">Optimization Benefits</h5>
                <ul class="list-unstyled mb-0">
                    <li class="py-2 border-bottom"><i class="bi bi-check-circle text-success me-2"></i> Faster route resolution</li>
                    <li class="py-2 border-bottom"><i class="bi bi-check-circle text-success me-2"></i> Config caching reduces file reads</li>
                    <li class="py-2 border-bottom"><i class="bi bi-check-circle text-success me-2"></i> View caching speeds up rendering</li>
                    <li class="py-2"><i class="bi bi-check-circle text-success me-2"></i> Event caching improves performance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection