@extends('layouts.chrissDashboardLayout')
@section('title', 'Manage Testimonials')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Customer Testimonials</h4>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Testimonial</a>
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
                    <th>Role</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Approved</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td><small class="text-muted">{{ $t->role }}</small></td>
                    <td><small>{{ Str::limit($t->message, 60) }}</small></td>
                    <td>{{ str_repeat('⭐', $t->rating) }}</td>
                    <td>
                        <span class="badge {{ $t->is_approved ? 'bg-success' : 'bg-warning' }}">
                            {{ $t->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $t->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $t->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.testimonials.toggle-approval', $t) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle Approval"><i class="bi bi-check-circle"></i></button>
                        </form>
                        <form action="{{ route('admin.testimonials.toggle-status', $t) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Toggle Visibility"><i class="bi bi-eye-slash"></i></button>
                        </form>
                        <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this testimonial?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No testimonials yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection