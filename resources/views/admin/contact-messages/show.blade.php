@extends('layouts.chrissDashboardLayout')
@section('title', 'Message from ' . $contactMessage->name)
@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif
<div class="card">
    <div class="card-body">
        <div class="mb-3 d-flex flex-wrap gap-2">
            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            @if(!$contactMessage->is_read)
                <form action="{{ route('admin.contact-messages.mark-read', $contactMessage) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i> Mark as Read</button>
                </form>
            @endif
            @if(!$contactMessage->is_approved)
                <form action="{{ route('admin.contact-messages.approve', $contactMessage) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Approve</button>
                </form>
                <form action="{{ route('admin.contact-messages.approve-convert', $contactMessage) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve & convert this message into a testimonial? It will appear on the homepage.')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-star"></i> Approve & Convert to Testimonial</button>
                </form>
            @elseif(!$contactMessage->converted_to_testimonial)
                <form action="{{ route('admin.contact-messages.approve-convert', $contactMessage) }}" method="POST" class="d-inline" onsubmit="return confirm('Convert this approved message into a testimonial?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-star"></i> Convert to Testimonial</button>
                </form>
            @endif
            <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
            </form>
        </div>
        <table class="table table-bordered">
            <tr><th style="width:150px">Name</th><td>{{ $contactMessage->name }}</td></tr>
            <tr><th>Email</th><td>{{ $contactMessage->email }}</td></tr>
            <tr><th>Phone</th><td>{{ $contactMessage->phone ?? 'N/A' }}</td></tr>
            <tr><th>Subject</th><td>{{ $contactMessage->subject ?? 'N/A' }}</td></tr>
            <tr><th>Date</th><td>{{ $contactMessage->created_at->format('d M Y, H:i') }}</td></tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge {{ $contactMessage->is_read ? 'bg-secondary' : 'bg-success' }}">{{ $contactMessage->is_read ? 'Read' : 'New' }}</span>
                    @if($contactMessage->is_approved)
                        <span class="badge bg-success">Approved</span>
                        @if($contactMessage->approved_at)
                            <small class="text-muted ms-1">({{ $contactMessage->approved_at->format('d M Y, H:i') }})</small>
                        @endif
                        @if($contactMessage->converted_to_testimonial)
                            <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill"></i> Converted to Testimonial</span>
                        @endif
                    @else
                        <span class="badge bg-warning text-dark">Pending Approval</span>
                    @endif
                </td>
            </tr>
        </table>
        <div class="mt-3">
            <h6>Message:</h6>
            <p class="p-3 bg-light rounded">{{ $contactMessage->message }}</p>
        </div>
    </div>
</div>
@endsection