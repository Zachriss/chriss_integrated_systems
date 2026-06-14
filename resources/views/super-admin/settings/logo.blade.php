@extends('super-admin.layouts.super-admin')

@section('title', 'Logo Upload')

@section('content')
<div class="sa-page-header">
    <h1>Logo Upload</h1>
    <p>Upload and manage system logo and favicon.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form method="POST" action="{{ route('super-admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">System Logo</label>
                    <input type="file" name="system_logo" class="form-control" accept="image/*">
                    @if($settings->system_logo)
                        <div class="mt-2">
                            <p class="small text-muted mb-1">Current Logo:</p>
                            <img src="{{ asset('storage/' . $settings->system_logo) }}" style="height:60px;border-radius:8px;">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Favicon</label>
                    <input type="file" name="system_favicon" class="form-control" accept="image/*">
                    @if($settings->system_favicon)
                        <div class="mt-2">
                            <p class="small text-muted mb-1">Current Favicon:</p>
                            <img src="{{ asset('storage/' . $settings->system_favicon) }}" style="height:32px;border-radius:4px;">
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-upload me-1"></i> Upload Logo</button>
                <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sa-outline btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
