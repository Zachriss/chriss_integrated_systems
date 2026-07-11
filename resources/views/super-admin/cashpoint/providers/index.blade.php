@extends('super-admin.layouts.super-admin')

@section('title', 'Manage Providers')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mobile Money Providers</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProviderModal">
                <i class="bi bi-plus-lg"></i> Add Provider
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $provider)
                            <tr>
                                <td>{{ $provider->id }}</td>
                                <td>{{ $provider->name }}</td>
                                <td><code>{{ $provider->code }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $provider->status === 'active' ? 'success' : 'danger' }}">
                                        {{ $provider->status }}
                                    </span>
                                </td>
                                <td>{{ $provider->creator?->name ?? 'N/A' }}</td>
                                <td>{{ $provider->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProviderModal{{ $provider->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('super-admin.cashpoint.providers.toggle-status', $provider) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-{{ $provider->status === 'active' ? 'secondary' : 'success' }}"
                                            onclick="return confirm('{{ $provider->status === 'active' ? 'Deactivate' : 'Activate' }} this provider?')">
                                            <i class="bi bi-{{ $provider->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('super-admin.cashpoint.providers.destroy', $provider) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete {{ $provider->name }}? This cannot be undone.')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No providers registered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $providers->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createProviderModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.cashpoint.providers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Register New Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Provider Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Airtel Money" required>
                </div>
                <div class="mb-3">
                    <label>Provider Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. AIRTEL" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals (rendered outside table to fix transparency issue) -->
@foreach($providers as $provider)
<div class="modal fade" id="editProviderModal{{ $provider->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.cashpoint.providers.update', $provider) }}" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $provider->name }}" required>
                </div>
                <div class="mb-3">
                    <label>Code</label>
                    <input type="text" name="code" class="form-control" value="{{ $provider->code }}" required>
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $provider->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $provider->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection