@extends('layouts.chrissDashboardLayout')
@section('title', 'Manage Links')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Quick Links & Navigation</h4>
    <a href="{{ route('admin.links.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Link</a>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Group</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                <tr>
                    <td>{{ $link->name }}</td>
                    <td><small class="text-muted">{{ Str::limit($link->url, 40) }}</small></td>
                    <td><span class="badge bg-info">{{ $link->group }}</span></td>
                    <td>{{ $link->order }}</td>
                    <td>
                        <span class="badge {{ $link->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $link->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.links.edit', $link) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.links.toggle-status', $link) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye-slash"></i></button>
                        </form>
                        <form action="{{ route('admin.links.destroy', $link) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this link?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No links added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection