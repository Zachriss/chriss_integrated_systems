@extends('super-admin.layouts.super-admin')

@section('title', 'System Preferences')

@section('content')
<div class="sa-page-header">
    <h1>System Preferences</h1>
    <p>Configure system preferences and email settings.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form method="POST" action="{{ route('super-admin.settings.update') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $settings->currency) }}" required>
                    @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-select">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ ($settings->timezone ?? 'Africa/Dar_es_Salaam') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email From Name</label>
                    <input type="text" name="email_from_name" class="form-control" value="{{ old('email_from_name', $settings->email_from_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email From Address</label>
                    <input type="email" name="email_from_address" class="form-control" value="{{ old('email_from_address', $settings->email_from_address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maintenance Mode</label>
                    <select name="maintenance_mode" class="form-select">
                        <option value="1" {{ $settings->maintenance_mode ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ !$settings->maintenance_mode ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Preferences</button>
                <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sa-outline btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
