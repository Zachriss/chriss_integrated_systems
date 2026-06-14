<div class="sidebar-section">
    <small>Main</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.contact-messages.index') }}" class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i>
            <span class="nav-text">Contact Messages</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            <span class="nav-text">Testimonials</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.links.index') }}" class="nav-link {{ request()->routeIs('admin.links.*') ? 'active' : '' }}">
            <i class="bi bi-link"></i>
            <span class="nav-text">Quick Links</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Operations</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('admin.staff-activities.staff-list') }}" class="nav-link {{ request()->routeIs('admin.staff-activities.staff-list') || request()->routeIs('admin.staff-activities.edit') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span class="nav-text">Staff Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.staff-tasks.index') }}" class="nav-link {{ request()->routeIs('admin.staff-tasks.*') ? 'active' : '' }}">
            <i class="bi bi-list-check"></i>
            <span class="nav-text">Task Assignments</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') && !request()->routeIs('admin.service-requests.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span class="nav-text">Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.service-requests.index') }}" class="nav-link {{ request()->routeIs('admin.service-requests.*') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i>
            <span class="nav-text">Customer Orders</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span class="nav-text">Inventory</span>
        </a>
    </li>
    <li class="nav-item has-submenu">
        <a href="#" class="nav-link nav-link-toggle {{ request()->routeIs('admin.cash-points.*') || request()->routeIs('admin.cashpoint.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#adminCashpointSubmenu" aria-expanded="{{ request()->routeIs('admin.cash-points.*') || request()->routeIs('admin.cashpoint.*') ? 'true' : 'false' }}">
            <i class="bi bi-cash-stack"></i>
            <span class="nav-text">Cash Point</span>
            <i class="bi bi-chevron-down submenu-arrow ms-auto"></i>
        </a>
        <div class="collapse submenu-collapse {{ request()->routeIs('admin.cash-points.*') || request()->routeIs('admin.cashpoint.*') ? 'show' : '' }}" id="adminCashpointSubmenu">
            <ul class="nav flex-column submenu-nav">
                <li class="nav-item">
                    <a href="{{ route('admin.cashpoint.index') }}" class="nav-link submenu-link {{ request()->routeIs('admin.cashpoint.index') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Staff Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.cashpoint.all-sessions') }}" class="nav-link submenu-link {{ request()->routeIs('admin.cashpoint.all-sessions') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">All Sessions</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>

<div class="sidebar-section">
    <small>Finance</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('admin.staff-reports.daily-income') }}" class="nav-link {{ request()->routeIs('admin.staff-reports.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i>
            <span class="nav-text">Daily Income</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
            <i class="bi bi-cart-dash"></i>
            <span class="nav-text">Expenses</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.finance.profit-loss') }}" class="nav-link {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i>
            <span class="nav-text">Profit Reports</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Monitoring</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            <span class="nav-text">Activity Logs</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span class="nav-text">Reports</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Account</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('dashboard.notifications.index') }}" class="nav-link {{ request()->routeIs('dashboard.notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-text">Notifications</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('dashboard.profile.show') }}" class="nav-link {{ request()->routeIs('dashboard.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span class="nav-text">Profile</span>
        </a>
    </li>
</ul>