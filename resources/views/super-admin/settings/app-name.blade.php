@extends('super-admin.layouts.super-admin')

@section('title', 'App Name')

@section('content')
<div class="sa-page-header">
    <h1>App Name</h1>
    <p>Configure the application name.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form method="POST" action="{{ route('super-admin.settings.update') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">System Name</label>
                <input type="text" name="system_name" class="form-control @error('system_name') is-invalid @enderror" value="{{ old('system_name', $settings->system_name) }}" required>
                @error('system_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save App Name</button>
                <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sa-outline btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
