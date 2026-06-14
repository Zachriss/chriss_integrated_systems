@php
    $currentUser = auth()->user();
    $notificationSource = $currentUser && method_exists($currentUser, 'notifications')
        ? $currentUser->notifications()
        : null;
    $headerNotifications = $notificationSource ? $notificationSource->latest()->limit(6)->get() : collect();
    $headerUnreadCount = $notificationSource ? (clone $notificationSource)->where('status', 'unread')->count() : 0;
    $displayName = $currentUser->full_name ?? $currentUser->name ?? 'User';
    $roleLabel = $currentUser?->isSuperAdmin() ? 'Super Admin' : ($currentUser?->isAdmin() ? 'Admin' : 'User');
@endphp

<header id="main-header" class="cis-app-header">
    <div class="cis-header-left">
        <button id="sidebarToggle" class="cis-header-menu" type="button" aria-label="Toggle sidebar" aria-expanded="true">
            <i id="sidebarToggleIcon" class="bi bi-list"></i>
        </button>

        <a href="{{ route('home') }}" class="cis-header-brand text-decoration-none">
            <img src="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}" alt="{{ $system_settings->system_name ?? 'System' }} Logo" class="cis-header-logo">
            <span class="cis-header-brand-copy">
                <span class="cis-header-title">{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</span>
                <span class="cis-header-subtitle">@yield('panel_subtitle', 'Enterprise Resource Planning')</span>
            </span>
        </a>
    </div>

    <div class="cis-header-right">
        <div class="dropdown">
            <button class="cis-header-icon-btn notification-trigger" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <i class="bi bi-bell-fill notification-trigger-icon"></i>
                <span id="notificationBadge" class="notification-badge badge rounded-pill bg-danger {{ $headerUnreadCount > 0 ? '' : 'd-none' }}">
                    {{ $headerUnreadCount > 9 ? '9+' : $headerUnreadCount }}
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 shadow-sm notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-menu-header d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                    <div>
                        <div class="fw-semibold">Notifications</div>
                        <div id="notificationUnreadCount" class="text-muted small">{{ $headerUnreadCount }} unread</div>
                    </div>
                    @if($headerUnreadCount > 0)
                        <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}" id="markAllReadForm">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">Mark all read</button>
                        </form>
                    @endif
                </div>
                <div id="notificationDropdownList" class="notification-menu-list">
                    @forelse ($headerNotifications as $notification)
                        @php
                            $typeIcon = match($notification->type ?? 'info') {
                                'success' => 'bi-check-circle-fill text-success',
                                'warning' => 'bi-exclamation-triangle-fill text-warning',
                                'error' => 'bi-x-circle-fill text-danger',
                                default => 'bi-info-circle-fill text-primary',
                            };
                        @endphp
                        <a href="{{ route('dashboard.notifications.show', $notification->id) }}" class="dropdown-item notification-item px-3 py-3 border-bottom {{ $notification->status === 'unread' ? 'notification-item-unread' : '' }}" data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi {{ $typeIcon }} mt-1 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-wrap notification-title">{{ $notification->title ?? 'System Notification' }}</div>
                                    <div class="text-muted small mb-1 notification-message">{{ $notification->message ?? '' }}</div>
                                    <div class="small text-muted notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-3 py-5 text-center text-muted notification-empty-state">
                            <i class="bi bi-bell fs-4 d-block mb-2"></i>
                            No notifications yet.
                        </div>
                    @endforelse
                </div>
                <div class="notification-menu-footer px-3 py-2 border-top text-end">
                    <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-sm notification-view-all-btn">View all</a>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <a href="#" class="cis-user-chip text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                @if(!empty($currentUser?->profile_image))
                    <img src="{{ asset('storage/' . $currentUser->profile_image) }}" alt="Profile Picture" class="profile-avatar">
                @else
                    <span class="cis-user-avatar">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                @endif
                <span class="cis-user-copy d-none d-md-flex">
                    <span class="cis-user-name">{{ $displayName }}</span>
                    <span class="cis-user-role">{{ $roleLabel }}</span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('dashboard.profile.show') }}">
                        <i class="bi bi-person-fill header-theme-icon me-2"></i> My Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center">
                            <i class="bi bi-box-arrow-right text-danger me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const badge = document.getElementById('notificationBadge');
    const unreadCountEl = document.getElementById('notificationUnreadCount');
    const dropdownList = document.getElementById('notificationDropdownList');
    const markAllForm = document.getElementById('markAllReadForm');

    function fetchNotifications() {
        fetch('{{ route("dashboard.notifications.dropdown-data") }}')
            .then(r => r.json())
            .then(data => {
                // Update badge
                if (data.unreadCount > 0) {
                    badge.textContent = data.unreadCount > 9 ? '9+' : data.unreadCount;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }

                // Update unread count text
                if (unreadCountEl) {
                    unreadCountEl.textContent = data.unreadCount + ' unread';
                }

                // Update dropdown list only if dropdown is open
                const dropdownMenu = document.querySelector('.notification-menu');
                if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                    renderDropdownItems(data);
                }
            })
            .catch(() => {});
    }

    function renderDropdownItems(data) {
        if (!dropdownList) return;

        if (data.notifications.length === 0) {
            dropdownList.innerHTML = `
                <div class="px-3 py-5 text-center text-muted notification-empty-state">
                    <i class="bi bi-bell fs-4 d-block mb-2"></i>
                    No notifications yet.
                </div>
            `;
            return;
        }

        dropdownList.innerHTML = data.notifications.map(n => {
            const typeIconMap = {
                'success': 'bi-check-circle-fill text-success',
                'warning': 'bi-exclamation-triangle-fill text-warning',
                'error': 'bi-x-circle-fill text-danger',
            };
            const icon = typeIconMap[n.type] || 'bi-info-circle-fill text-primary';
            const unreadClass = n.status === 'unread' ? 'notification-item-unread' : '';

            return `
                <a href="${n.open_url}" class="dropdown-item notification-item px-3 py-3 border-bottom ${unreadClass}" data-notification-id="${n.id}">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi ${icon} mt-1 flex-shrink-0"></i>
                        <div class="min-w-0">
                            <div class="fw-semibold text-wrap notification-title">${n.title}</div>
                            <div class="text-muted small mb-1 notification-message">${n.message}</div>
                            <div class="small text-muted notification-time">${n.time}</div>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    }

    // Mark all read via AJAX
    if (markAllForm) {
        markAllForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            }).then(() => {
                // Immediately update UI
                badge.classList.add('d-none');
                if (unreadCountEl) unreadCountEl.textContent = '0 unread';

                // Remove unread styling from all items
                document.querySelectorAll('.notification-item').forEach(el => {
                    el.classList.remove('notification-item-unread');
                });

                // Re-fetch to sync
                fetchNotifications();
            }).catch(() => {
                // Fallback: submit normally
                form.submit();
            });
        });
    }

    // Poll every 30 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    // Refresh when dropdown opens
    const dropdownTrigger = document.getElementById('notificationDropdown');
    if (dropdownTrigger) {
        dropdownTrigger.addEventListener('shown.bs.dropdown', function() {
            // Re-render items with fresh data on dropdown open
            fetch('{{ route("dashboard.notifications.dropdown-data") }}')
                .then(r => r.json())
                .then(data => {
                    renderDropdownItems(data);
                    // Update badge too
                    if (data.unreadCount > 0) {
                        badge.textContent = data.unreadCount > 9 ? '9+' : data.unreadCount;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                    if (unreadCountEl) {
                        unreadCountEl.textContent = data.unreadCount + ' unread';
                    }
                })
                .catch(() => {});
        });
    }
});
</script>
@endpush