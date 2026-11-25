<div class="sidebar-wrapper" id="sidebar">
    <a href="{{ route('rider.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('storage/'.ltrim($webSetting['Website Header Logo'], '/')) }}"
            class="site-logo site-logo-header" alt="Bazaar Chalo" width="50" height="50">
        <span>Bazaar Chalo</span>
    </a>

    <div class="flex-grow-1 overflow-y-auto">
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="{{ route('rider.dashboard') }}"
                        class="sidebar-menu-link {{ request()->routeIs('rider.dashboard') ? 'active' : '' }}"
                        id="menu-overview" title="Dashboard">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('rider.orders.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('rider.orders.*') ? 'active' : '' }}"
                        id="menu-orders" title="Orders">
                        <i class="bi bi-receipt"></i>
                        <span>Orders</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-profile">
        <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : asset('assets/images/no_image.png') }}"
            alt="{{ auth()->user()->name }}" class="sidebar-profile-img"
            onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-profile-email">{{ auth()->user()->email }}</div>
        </div>
    </div>
</div>
