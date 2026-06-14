@extends('super-admin.layouts.super-admin')

@section('title', 'Maintenance Mode')

@section('content')
<div class="sa-page-header">
    <h1>Maintenance Mode</h1>
    <p>Enable or disable system maintenance mode.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body text-center py-5">
                <i class="bi bi-shield-exclamation" style="font-size:2.5rem;color:#dc2626;"></i>
                <h5 class="fw-bold mt-3 mb-2">Maintenance Mode</h5>
                <p class="text-muted small mb-3">When enabled, only users with the secret bypass token can access the site.</p>
                <form method="POST" action="{{ route('super-admin.maintenance.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="down">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-shield-slash me-2"></i> Enable Maintenance</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sa-card">
            <div class="sa-card-body text-center py-5">
                <i class="bi bi-check-circle" style="font-size:2.5rem;color:#166534;"></i>
                <h5 class="fw-bold mt-3 mb-2">Disable Maintenance</h5>
                <p class="text-muted small mb-3">Restore normal system access for all users.</p>
                <form method="POST" action="{{ route('super-admin.maintenance.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="up">
                    <button type="submit" class="btn btn-sa-primary"><i class="bi bi-play-circle me-2"></i> Disable Maintenance</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection