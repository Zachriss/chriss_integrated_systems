@extends('layouts.chrissDashboardLayout')
@section('title', 'Contact Messages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Contact Messages 
        @if($unreadCount > 0)<span class="badge bg-danger ms-2">{{ $unreadCount }} unread</span>@endif
        @if($pendingCount > 0)<span class="badge bg-warning text-dark ms-1">{{ $pendingCount }} pending</span>@endif
    </h4>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif
<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                <tr class="{{ !$msg->is_read ? 'fw-bold' : '' }} {{ $msg->is_approved ? '' : 'table-secondary' }}">
                    <td>{{ $msg->name }}</td>
                    <td><small>{{ $msg->email }}</small></td>
                    <td><small>{{ $msg->subject ?? 'N/A' }}</small></td>
                    <td><small>{{ Str::limit($msg->message, 50) }}</small></td>
                    <td>
                        <span class="badge {{ $msg->is_read ? 'bg-secondary' : 'bg-success' }}">
                            {{ $msg->is_read ? 'Read' : 'New' }}
                        </span>
                    </td>
                    <td>
                        @if($msg->is_approved)
                            <span class="badge bg-success">Approved</span>
                            @if($msg->converted_to_testimonial)
                                <small class="d-block text-muted mt-1"><i class="bi bi-star-fill text-warning"></i> Testimonial</small>
                            @endif
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td><small>{{ $msg->created_at->format('d M Y') }}</small></td>
                    <td>
                        <a href="{{ route('admin.contact-messages.show', $msg) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        @if(!$msg->is_approved)
                            <form action="{{ route('admin.contact-messages.approve', $msg) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <form action="{{ route('admin.contact-messages.approve-convert', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve & convert this message into a testimonial? It will appear on the homepage.')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Approve & Convert to Testimonial"><i class="bi bi-star"></i></button>
                            </form>
                        @elseif(!$msg->converted_to_testimonial)
                            <form action="{{ route('admin.contact-messages.approve-convert', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Convert this approved message into a testimonial?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Convert to Testimonial"><i class="bi bi-star"></i></button>
                            </form>
                        @endif
                        <form action="{{ route('admin.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">No messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection