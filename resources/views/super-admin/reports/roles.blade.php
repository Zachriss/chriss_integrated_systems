@extends('super-admin.layouts.super-admin')

@section('title', 'Roles Report')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div><h1>Roles Report</h1><p>Overview of roles and their permissions.</p></div>
    <div class="d-flex gap-2">
        <a class="btn btn-sa-outline btn-outline-primary" href="{{ route('super-admin.reports.export', ['report' => 'roles', 'format' => 'pdf']) }}"><i class="bi bi-filetype-pdf me-1"></i> PDF</a>
        <a class="btn btn-sa-outline btn-outline-success" href="{{ route('super-admin.reports.export', ['report' => 'roles', 'format' => 'excel']) }}"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    </div>
</div>
<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead><tr><th>Role</th><th>Slug</th><th>Users</th><th>Permissions</th></tr></thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td class="fw-semibold">{{ $role->name }}</td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td><span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;">{{ $role->users_count }}</span></td>
                        <td><span class="sa-badge" style="background:rgba(139,92,246,0.1);color:#8b5cf6;">{{ $role->permissions_count }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
