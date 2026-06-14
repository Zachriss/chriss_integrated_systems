@extends('layouts.chrissDashboardLayout')
@section('title', 'Edit Link')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.links.update', $link) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $link->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL</label>
                    <input type="text" name="url" class="form-control" value="{{ old('url', $link->url) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Group <span class="text-danger">*</span></label>
                    <select name="group" class="form-select" required>
                        <option value="quick_links" {{ $link->group == 'quick_links' ? 'selected' : '' }}>Quick Links</option>
                        <option value="services" {{ $link->group == 'services' ? 'selected' : '' }}>Services</option>
                        <option value="footer" {{ $link->group == 'footer' ? 'selected' : '' }}>Footer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', $link->icon) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', $link->order) }}" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $link->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$link->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Link</button>
                <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection