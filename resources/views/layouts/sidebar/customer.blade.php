<div class="sidebar-section">
    <small>Customer Menu</small>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('customer.services.index') }}" class="nav-link {{ request()->routeIs('customer.services.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span class="nav-text">Browse Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('customer.my-requests') }}" class="nav-link {{ request()->routeIs('customer.my-requests') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i>
            <span class="nav-text">My Requests</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('customer.products.browse') }}" class="nav-link {{ request()->routeIs('customer.products.*') ? 'active' : '' }}">
            <i class="bi bi-cart-plus"></i>
            <span class="nav-text">Order Products</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('customer.my-products') }}" class="nav-link {{ request()->routeIs('customer.my-products') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span class="nav-text">My Products</span>
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
