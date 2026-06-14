<header class="header-wrapper">
    <div class="header-container">
        <div class="header-branding">
             <div class="header-logo">
                <img src="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}" alt="{{ $system_settings->system_name ?? 'System' }} Logo" style="width: 38px; height: 38px; border-radius: 8px;">
            </div>
            <span class="header-name">
                <span class="header-title">{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</span>
                <span class="header-subtitle">ERP Platform</span>
            </span>
        </div>

        <button class="header-toggle" id="navToggle" aria-label="Toggle Menu" aria-expanded="false" aria-controls="mainNav">
            <span class="header-toggle-line"></span>
            <span class="header-toggle-line"></span>
            <span class="header-toggle-line"></span>
        </button>

        <nav class="header-nav" id="mainNav">
            @php
                $currentPath = trim(request()->path(), '/');
            @endphp
            <ul>
                <li><a href="{{ route('home') }}" class="{{ $currentPath === '' ? 'active' : '' }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li><a href="{{ route('home') }}#services"><i class="bi bi-grid"></i> Services</a></li>
                <li><a href="{{ route('home') }}#products"><i class="bi bi-bag"></i> Products</a></li>
                <li><a href="{{ route('about') }}" class="{{ $currentPath === 'about' ? 'active' : '' }}"><i class="bi bi-info-circle"></i> About</a></li>
                <li><a href="{{ route('home') }}#contact"><i class="bi bi-telephone"></i> Contact</a></li>
                @guest
                    <li><a href="{{ route('login') }}" class="{{ $currentPath === 'login' ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Login</a></li>
                    <li><a href="{{ route('customer.register') }}"><i class="bi bi-person-plus"></i> Register</a></li>
                @else
                    <li><a href="{{ route('dashboard.index') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" style="border:0;background:transparent;color:inherit;font:inherit;padding:0;">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </nav>
    </div>
</header>