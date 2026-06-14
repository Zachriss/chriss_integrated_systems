@php
    $user = auth()->user();
@endphp

<aside id="sidebar" class="chriss-sidebar">
    <div class="chriss-sidebar-inner">
        <div class="sidebar-brand">
            <i class="bi bi-grid-fill"></i>
            <span class="brand-text">Chriss ERP</span>
        </div>

        @if($user)
            @php
                $sidebarFile = match($user->role) {
                    'super_admin' => 'layouts.sidebar.super-admin',
                    'admin' => 'layouts.sidebar.admin',
                    'staff' => 'layouts.sidebar.staff',
                    'customer' => 'layouts.sidebar.customer',
                    default => null
                };
            @endphp

            @if($sidebarFile)
                @include($sidebarFile)
            @endif
        @endif

        <div class="chriss-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="chriss-sidebar-logout">
                    <i class="bi bi-box-arrow-left"></i>
                    <span class="nav-text">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>