@extends('layouts.chrissDashboardLayout')
@section('title', 'Edit Testimonial')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $testimonial->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role', $testimonial->role) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="3" required>{{ old('message', $testimonial->message) }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @if($testimonial->image)
                        <small class="text-muted">Current: {{ $testimonial->image }}</small>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">Approved</label>
                    <select name="is_approved" class="form-select">
                        <option value="1" {{ $testimonial->is_approved ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$testimonial->is_approved ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $testimonial->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$testimonial->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Testimonial</button>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection