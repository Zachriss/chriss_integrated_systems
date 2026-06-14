@extends('layouts.chrissDashboardLayout')
@section('title', 'Add Testimonial')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role') }}" placeholder="e.g. Customer">
                </div>
                <div class="col-12">
                    <label class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="3" required>{{ old('message') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ old('rating', 5) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Approved</label>
                    <select name="is_approved" class="form-select">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Testimonial</button>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection