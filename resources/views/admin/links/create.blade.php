@extends('layouts.chrissDashboardLayout')
@section('title', 'Add Link')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.links.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL</label>
                    <input type="text" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Group <span class="text-danger">*</span></label>
                    <select name="group" class="form-select" required>
                        <option value="quick_links">Quick Links</option>
                        <option value="services">Services</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon (Bootstrap icon class)</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="bi bi-link">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Link</button>
                <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection