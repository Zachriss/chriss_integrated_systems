@extends('super-admin.layouts.super-admin')

@section('title', 'System Notifications')

@section('content')
<div class="sa-page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>System Notifications</h1>
        <p>View and manage system-wide broadcast notifications.</p>
    </div>
    <button class="btn btn-sa-primary"><i class="bi bi-plus-lg me-1"></i> New Notification</button>
</div>

<div class="sa-card">
    <div class="sa-card-body p-0">
        <div class="table-responsive">
            <table class="table sa-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Audience</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(App\Models\Notification::latest()->get() as $notification)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $notification->title ?? 'Untitled' }}</div>
                        </td>
                        <td><span class="sa-badge" style="background:#eef2ff;color:#4f46e5;">{{ $notification->type ?? 'system' }}</span></td>
                        <td>{{ $notification->audience ?? 'All Users' }}</td>
                        <td>
                            <span class="sa-badge {{ $notification->status === 'sent' ? 'sa-badge-active' : 'sa-badge-inactive' }}">
                                {{ $notification->status ?? 'draft' }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $notification->created_at?->diffForHumans() ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-sa-outline btn-outline-primary" title="View"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-sa-outline btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No notifications created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection