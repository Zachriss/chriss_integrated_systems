@extends('super-admin.layouts.super-admin')

@section('title', 'Create Role')

@section('content')
<div class="sa-page-header">
    <h1>Create Role</h1>
    <p>Define a new role and assign permissions.</p>
</div>

<div class="sa-card">
    <div class="sa-card-body">
        <form method="POST" action="{{ route('super-admin.roles.store') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                </div>
            </div>

            <h6 class="fw-semibold mb-3">Assign Permissions</h6>
            @foreach($permissions as $module => $modulePermissions)
            <div class="mb-3">
                <h6 style="font-size:0.82rem;text-transform:uppercase;color:#64748b;letter-spacing:0.05em;">{{ $module }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($modulePermissions as $permission)
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:0.35rem 0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.85rem;">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        {{ $permission->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="mt-4">
                <button type="submit" class="btn btn-sa-primary"><i class="bi bi-check2 me-1"></i> Create Role</button>
                <a href="{{ route('super-admin.roles.index') }}" class="btn btn-sa-outline btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection