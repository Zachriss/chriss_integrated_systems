@extends('super-admin.layouts.super-admin')

@section('title', 'System Settings')

@section('content')
<div class="sa-page-header">
    <h1>System Settings</h1>
    <p>Configure global system settings, branding, social media, and homepage content.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
    {{-- Branding & Identity --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-brush me-2"></i> Branding & Identity</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-branding') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">System Name</label>
                            <input type="text" name="system_name" class="form-control @error('system_name') is-invalid @enderror" value="{{ old('system_name', $settings->system_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Short Name</label>
                            <input type="text" name="system_short_name" class="form-control" value="{{ old('system_short_name', $settings->system_short_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">System Description</label>
                            <input type="text" name="system_description" class="form-control" value="{{ old('system_description', $settings->system_description) }}" placeholder="Tagline for footer">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $settings->primary_color) }}" style="height:42px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $settings->secondary_color) }}" style="height:42px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Accent Color</label>
                            <input type="color" name="accent_color" class="form-control form-control-color" value="{{ old('accent_color', $settings->accent_color ?? '#0d6efd') }}" style="height:42px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings->currency) }}" required>
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
                            <label class="form-label">System Logo</label>
                            <input type="file" name="system_logo" class="form-control" accept="image/*">
                            @if($settings->system_logo) 
                                <div class="mt-2"><img src="{{ asset('storage/' . $settings->system_logo) }}" style="height:40px;border-radius:8px;"></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Favicon</label>
                            <input type="file" name="system_favicon" class="form-control" accept="image/*">
                            @if($settings->system_favicon) 
                                <div class="mt-2"><img src="{{ asset('storage/' . $settings->system_favicon) }}" style="height:32px;border-radius:4px;" alt="Favicon preview"></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Login Background</label>
                            <input type="file" name="login_background" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maintenance Mode</label>
                            <select name="maintenance_mode" class="form-select">
                                <option value="1" {{ $settings->maintenance_mode ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ !$settings->maintenance_mode ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Branding</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-telephone me-2"></i> Contact Information</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-contact') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $settings->address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email (for homepage)</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings->contact_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone (for homepage)</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings->contact_phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contact Address (for homepage)</label>
                            <input type="text" name="contact_address" class="form-control" value="{{ old('contact_address', $settings->contact_address) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Map Embed URL (HTML iframe)</label>
                            <textarea name="map_embed_url" class="form-control" rows="2">{{ old('map_embed_url', $settings->map_embed_url) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Social Media Links --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-share me-2"></i> Social Media Links</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-social') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Facebook URL</label>
                            <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Twitter / X URL</label>
                            <input type="url" name="twitter_url" class="form-control" value="{{ old('twitter_url', $settings->twitter_url) }}" placeholder="https://twitter.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram URL</label>
                            <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">YouTube URL</label>
                            <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="+255...">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Social Links</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hero Section --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-images me-2"></i> Hero Section (Homepage)</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-hero') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $settings->hero_title) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle', $settings->hero_subtitle) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hero Image</label>
                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                            @if($settings->hero_image)
                                <div class="mt-2"><img src="{{ asset('storage/' . $settings->hero_image) }}" style="max-height:60px;border-radius:8px;"></div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Hero</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- About Section --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-info-circle me-2"></i> About Section</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-about') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">About Description</label>
                            <textarea name="about_description" class="form-control" rows="4">{{ old('about_description', $settings->about_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">About Image</label>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            @if($settings->about_image)
                                <div class="mt-2"><img src="{{ asset('storage/' . $settings->about_image) }}" style="max-height:60px;border-radius:8px;"></div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save About</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="col-lg-6">
        <div class="sa-card sa-card-section">
            <div class="sa-card-header">
                <h5 class="sa-card-title"><i class="bi bi-file-text me-2"></i> Footer</h5>
            </div>
            <div class="sa-card-body">
                <form method="POST" action="{{ route('super-admin.settings.update-footer') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Footer Text</label>
                            <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $settings->footer_text) }}">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sa-primary"><i class="bi bi-save me-1"></i> Save Footer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.sa-card-section {
    height: 100%;
    transition: box-shadow 0.2s ease;
}
.sa-card-section:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.sa-card-section .sa-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    background: transparent;
}
.sa-card-section .sa-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--sa-primary, #4361ee);
}
.sa-card-section .sa-card-body {
    padding: 1.25rem;
}
.sa-card-section .form-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.35rem;
}
.sa-card-section .btn-sa-primary {
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
    border-radius: 8px;
}
</style>
@endpush