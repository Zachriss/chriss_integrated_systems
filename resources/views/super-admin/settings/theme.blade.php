@extends('super-admin.layouts.super-admin')

@section('title', 'Theme Color')

@section('content')
<div class="sa-page-header">
    <h1>Theme Color</h1>
    <p>Configure the application theme colors.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form method="POST" action="{{ route('super-admin.settings.update') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $settings->primary_color) }}" style="height:42px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Color</label>
                    <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $settings->secondary_color) }}" style="height:42px;">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-palette me-1"></i> Save Theme</button>
                <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sa-outline btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
