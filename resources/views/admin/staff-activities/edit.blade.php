@extends('layouts.chrissDashboardLayout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Staff Member</h4>
            <p class="text-muted mb-0 small">Update staff details and settings</p>
        </div>
        <a href="{{ route('admin.staff-activities.staff-list') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff List
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> Please fix the following errors:
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Staff Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.staff-activities.update', $staff) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $staff->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="active" {{ old('status', $staff->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $staff->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" name="department" id="department" class="form-control" 
                                       value="{{ old('department', $staff->staffProfile->department ?? '') }}"
                                       placeholder="e.g. IT, Electrical, Cashier">
                            </div>
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Salary (TZS)</label>
                                <input type="number" name="salary" id="salary" class="form-control" 
                                       value="{{ old('salary', $staff->staffProfile->salary ?? '') }}" 
                                       min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Update Staff
                            </button>
                            <a href="{{ route('admin.staff-activities.staff-list') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Staff Summary</h5>
                </div>
                <div class="card-body text-center">
                    <div class="rounded-circle bg-{{ $staff->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <span class="fw-bold text-{{ $staff->status === 'active' ? 'success' : 'secondary' }}" style="font-size: 1.5rem;">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </span>
                    </div>
                    <h5>{{ $staff->name }}</h5>
                    <p class="text-muted small mb-1">{{ $staff->email }}</p>
                    <span class="badge bg-{{ $staff->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($staff->status) }}
                    </span>
                    <hr>
                    <div class="text-start small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Role:</span>
                            <span class="fw-semibold">{{ ucfirst($staff->role) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Department:</span>
                            <span class="fw-semibold">{{ $staff->staffProfile->department ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Assigned Categories:</span>
                            <span class="fw-semibold">{{ $staff->categoryAssignments->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Joined:</span>
                            <span class="fw-semibold">{{ $staff->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection