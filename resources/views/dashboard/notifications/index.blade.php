@extends('layouts.chrissDashboardLayout')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">All Notifications</h5>
                        <p class="text-muted small mb-0">
                            <span id="totalUnread">{{ $unreadCount }}</span> unread notification{{ $unreadCount !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}" id="bulkMarkAllRead">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-check2-all me-1"></i> Mark all read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Filter tabs -->
                <ul class="nav nav-pills mb-4 gap-1" id="notificationFilters" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dashboard.notifications.index', ['filter' => 'all']) }}"
                           class="nav-link {{ $filter === 'all' ? 'active' : '' }}"
                           data-filter="all">
                            All
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dashboard.notifications.index', ['filter' => 'unread']) }}"
                           class="nav-link {{ $filter === 'unread' ? 'active' : '' }}"
                           data-filter="unread">
                            Unread
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dashboard.notifications.index', ['filter' => 'read']) }}"
                           class="nav-link {{ $filter === 'read' ? 'active' : '' }}"
                           data-filter="read">
                            Read
                        </a>
                    </li>
                </ul>

                <!-- Notifications list -->
                <div id="notificationsList">
                    @forelse ($notifications as $notification)
                        @php
                            $typeIcon = match($notification->type ?? 'info') {
                                'success' => 'bi-check-circle-fill text-success',
                                'warning' => 'bi-exclamation-triangle-fill text-warning',
                                'error' => 'bi-x-circle-fill text-danger',
                                default => 'bi-info-circle-fill text-primary',
                            };
                        @endphp
                        <a href="{{ route('dashboard.notifications.show', $notification->id) }}"
                           class="text-decoration-none text-reset d-block notification-row {{ $notification->status === 'unread' ? 'notification-row-unread' : '' }}"
                           data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start gap-3 py-3 px-3 border-bottom notification-row-inner">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="bi {{ $typeIcon }} fs-5"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <span class="fw-semibold notification-row-title">{{ $notification->title ?? 'System Notification' }}</span>
                                        <small class="text-muted text-nowrap notification-row-time">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-muted small mt-1 notification-row-message">{{ $notification->message ?? '' }}</div>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $notification->status === 'unread' ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $notification->status === 'unread' ? 'primary' : 'secondary' }} notification-row-status">
                                            {{ ucfirst($notification->status ?? 'unknown') }}
                                        </span>
                                    </div>
                                </div>
                                @if($notification->status === 'unread')
                                    <div class="flex-shrink-0 mt-2">
                                        <span class="unread-dot"></span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">
                                @if($filter === 'unread')
                                    No unread notifications.
                                @elseif($filter === 'read')
                                    No read notifications.
                                @else
                                    No notifications yet.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->appends(['filter' => $filter])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all read AJAX
    const bulkMarkForm = document.getElementById('bulkMarkAllRead');
    if (bulkMarkForm) {
        bulkMarkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
            }).then(() => {
                // Reload the page to reflect changes
                window.location.reload();
            }).catch(() => {
                form.submit();
            });
        });
    }
});
</script>
@endpush