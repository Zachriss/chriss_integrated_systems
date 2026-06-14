@extends('super-admin.layouts.super-admin')

@section('title', 'Users Report')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Users Report</h1>
        <p>Detailed user information filtered by role and status.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-sa-outline btn-outline-primary" href="{{ route('super-admin.reports.export', ['report' => 'users', 'format' => 'pdf'] + request()->query()) }}"><i class="bi bi-filetype-pdf me-1"></i> PDF</a>
        <a class="btn btn-sa-outline btn-outline-success" href="{{ route('super-admin.reports.export', ['report' => 'users', 'format' => 'excel'] + request()->query()) }}"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    </div>
</div>

<div class="sa-card mb-3">
    <div class="sa-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->slug }}" {{ request('role') === $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">From</label><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="col-md-2"><label class="form-label">To</label><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-sa-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="sa-badge" style="background:rgba(26,115,232,0.1);color:#1a73e8;text-transform:capitalize;">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">None</span>
                            @endforelse
                        </td>
                        <td><span class="sa-badge {{ $user->status === 'active' ? 'sa-badge-active' : 'sa-badge-inactive' }}">{{ $user->status }}</span></td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
