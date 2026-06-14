<div class="sidebar-section">
    <small>Overview</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>User Management</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('super-admin.users.index') }}" class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span class="nav-text">Manage Users</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.roles.index') }}" class="nav-link {{ request()->routeIs('super-admin.roles.*') || request()->routeIs('super-admin.permissions.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock-fill"></i>
            <span class="nav-text">Roles & Permissions</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Operations</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item has-submenu">
        <a href="#" class="nav-link nav-link-toggle {{ request()->routeIs('super-admin.cash-points.*') || request()->routeIs('super-admin.cashpoint.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#superAdminCashpointSubmenu" aria-expanded="{{ request()->routeIs('super-admin.cash-points.*') || request()->routeIs('super-admin.cashpoint.*') ? 'true' : 'false' }}">
            <i class="bi bi-cash-stack"></i>
            <span class="nav-text">Cash Point</span>
            <i class="bi bi-chevron-down submenu-arrow ms-auto"></i>
        </a>
        <div class="collapse submenu-collapse {{ request()->routeIs('super-admin.cash-points.*') || request()->routeIs('super-admin.cashpoint.*') ? 'show' : '' }}" id="superAdminCashpointSubmenu">
            <ul class="nav flex-column submenu-nav">
                <li class="nav-item">
                    <a href="{{ route('super-admin.cashpoint.dashboard') }}" class="nav-link submenu-link {{ request()->routeIs('super-admin.cashpoint.dashboard') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('super-admin.cashpoint.sessions') }}" class="nav-link submenu-link {{ request()->routeIs('super-admin.cashpoint.sessions') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">All Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('super-admin.cashpoint.providers') }}" class="nav-link submenu-link {{ request()->routeIs('super-admin.cashpoint.providers*') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Providers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('super-admin.cashpoint.audit-logs') }}" class="nav-link submenu-link {{ request()->routeIs('super-admin.cashpoint.audit-logs') ? 'active' : '' }}">
                        <span class="nav-text submenu-text">Audit Logs</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.services.index') }}" class="nav-link {{ request()->routeIs('super-admin.services.index') || request()->routeIs('super-admin.services.store') || request()->routeIs('super-admin.services.show') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span class="nav-text">Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.services.categories') }}" class="nav-link {{ request()->routeIs('super-admin.services.categories*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i>
            <span class="nav-text">Service Categories</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.inventory.products') }}" class="nav-link {{ request()->routeIs('super-admin.inventory.products*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i>
            <span class="nav-text">Products</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.inventory.categories') }}" class="nav-link {{ request()->routeIs('super-admin.inventory.categories*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i>
            <span class="nav-text">Categories</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.inventory.stock-in') }}" class="nav-link {{ request()->routeIs('super-admin.inventory.stock-in*') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-right"></i>
            <span class="nav-text">Stock In</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.inventory.stock-out') }}" class="nav-link {{ request()->routeIs('super-admin.inventory.stock-out*') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-right"></i>
            <span class="nav-text">Stock Out</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.inventory.reports') }}" class="nav-link {{ request()->routeIs('super-admin.inventory.reports*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i>
            <span class="nav-text">Stock Reports</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Online Shop</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('shop.index') }}" target="_blank" class="nav-link">
            <i class="bi bi-cart-fill"></i>
            <span class="nav-text">View Shop</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Monitoring</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('super-admin.reports.index') }}" class="nav-link {{ request()->routeIs('super-admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i>
            <span class="nav-text">Reports</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('super-admin.audit-logs.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span class="nav-text">Audit Logs</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Maintenance</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('super-admin.backups.index') }}" class="nav-link {{ request()->routeIs('super-admin.backups.*') ? 'active' : '' }}">
            <i class="bi bi-database-fill"></i>
            <span class="nav-text">Backup & Restore</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.notifications.system') }}" class="nav-link {{ request()->routeIs('super-admin.notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-text">Notifications</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.maintenance.clear-cache') }}" class="nav-link {{ request()->routeIs('super-admin.maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools"></i>
            <span class="nav-text">System Maintenance</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>CMS Management</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('admin.contact-messages.index') }}" class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i>
            <span class="nav-text">Contact Messages</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
            <i class="bi bi-star-fill"></i>
            <span class="nav-text">Testimonials</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.links.index') }}" class="nav-link {{ request()->routeIs('admin.links.*') ? 'active' : '' }}">
            <i class="bi bi-link-45deg"></i>
            <span class="nav-text">Quick Links</span>
        </a>
    </li>
</ul>

<div class="sidebar-section">
    <small>Account</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('dashboard.profile.show') }}" class="nav-link {{ request()->routeIs('dashboard.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span class="nav-text">Profile</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('super-admin.settings.index') }}" class="nav-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i>
            <span class="nav-text">System Settings</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/') }}#about-section" class="nav-link">
            <i class="bi bi-info-circle-fill"></i>
            <span class="nav-text">About Section</span>
        </a>
    </li>
</ul>