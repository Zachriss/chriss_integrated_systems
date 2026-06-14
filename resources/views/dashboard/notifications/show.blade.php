@extends('layouts.chrissDashboardLayout')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="mb-3">
            <a href="{{ route('dashboard.notifications.index') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Back to notifications
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @php
                    $typeIcon = match($notification->type ?? 'info') {
                        'success' => 'bi-check-circle-fill text-success',
                        'warning' => 'bi-exclamation-triangle-fill text-warning',
                        'error' => 'bi-x-circle-fill text-danger',
                        default => 'bi-info-circle-fill text-primary',
                    };
                @endphp

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <i class="bi {{ $typeIcon }} fs-2"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h4 class="mb-1">{{ $notification->title ?? 'Notification' }}</h4>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge bg-{{ $notification->status === 'unread' ? 'primary' : 'secondary' }}">
                                {{ ucfirst($notification->status ?? 'unknown') }}
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ $notification->created_at ? $notification->created_at->format('F j, Y \a\t g:i A') : 'N/A' }}
                                ({{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }})
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="notification-detail-body">
                    <p class="mb-0">{{ $notification->message ?? 'No message available.' }}</p>
                </div>

                @if($notification->type)
                    <div class="mt-4">
                        <small class="text-muted">
                            <strong>Type:</strong>
                            <span class="badge bg-info bg-opacity-10 text-info ms-1">{{ ucfirst($notification->type) }}</span>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection