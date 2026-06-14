@extends('super-admin.layouts.super-admin')

@section('title', 'System Configuration')

@section('content')
<div class="sa-page-header">
    <h1>System Configuration</h1>
    <p>Manage core system configuration values.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Application Name</label>
                    <input type="text" class="form-control" value="{{ config('app.name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Application URL</label>
                    <input type="text" class="form-control" value="{{ config('app.url') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Session Driver</label>
                    <input type="text" class="form-control" value="{{ config('session.driver') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Session Lifetime (minutes)</label>
                    <input type="number" class="form-control" value="{{ config('session.lifetime') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Debug Mode</label>
                    <select class="form-select">
                        <option value="true" {{ config('app.debug') ? 'selected' : '' }}>Enabled</option>
                        <option value="false" {{ !config('app.debug') ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-check2 me-1"></i> Save Configuration</button>
            </div>
        </form>
    </div>
</div>
@endsection