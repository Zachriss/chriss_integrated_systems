@extends('super-admin.layouts.super-admin')

@section('title', 'Manage Users')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Manage Users</h1>
        <p>Create, edit, and manage system users.</p>
    </div>
    <a href="{{ route('super-admin.users.create') }}" class="btn btn-sa-primary"><i class="bi bi-plus-lg me-1"></i> New User</a>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Roles</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $user->name }}</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">{{ $user->full_name }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="sa-badge {{ $user->status === 'active' ? 'sa-badge-active' : 'sa-badge-inactive' }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>
                            <span class="sa-badge" style="background:rgba(139,92,246,0.1);color:#8b5cf6;">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-sm btn-sa-outline btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-warning" title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $user->status === 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @if(!($user->isSuperAdmin() && $user->id !== auth()->id()))
                                <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-sa-outline btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="sa-card-body border-top">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
