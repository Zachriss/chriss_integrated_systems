@extends('super-admin.layouts.super-admin')

@section('title', 'Roles & Permissions')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Roles & Permissions</h1>
        <p>Manage system roles and their permissions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('super-admin.roles.create') }}" class="btn btn-sa-primary"><i class="bi bi-plus-lg me-1"></i> New Role</a>
        <a href="{{ route('super-admin.permissions.index') }}" class="btn btn-sa-outline btn-outline-primary"><i class="bi bi-shield me-1"></i> Permission Matrix</a>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead>
                    <tr><th>Role Name</th><th>Slug</th><th>Description</th><th>Permissions</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td class="fw-semibold">{{ $role->name }}</td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td style="max-width:250px;">{{ $role->description ?? '—' }}</td>
                        <td><span class="sa-badge" style="background:rgba(139,92,246,0.1);color:#8b5cf6;">{{ $role->permissions->count() }} permissions</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-sm btn-sa-outline btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                @if($role->slug !== 'super-admin')
                                <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection