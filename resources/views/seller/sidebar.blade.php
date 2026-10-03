<div class="sidebar-wrapper" id="sidebar">
    <!-- Brand Logo / Identity -->
    <a href="dashboard.html" class="sidebar-brand">
        <i class="bi bi-asterisk"></i>
        <span>Bazaar Chalo</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto">
        <!-- Group: Menu -->
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="{{route('seller.dashboard')}}" class="sidebar-menu-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" id="menu-overview" title="Overview">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('seller.shops.index') }}" class="sidebar-menu-link {{ request()->routeIs('seller.shops.*') ? 'active' : '' }}" id="menu-shops" title="Shops">
                        <i class="bi bi-shop"></i>
                        <span>Shops</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('seller.collections.index') }}" class="sidebar-menu-link {{ request()->routeIs('seller.collections.*') ? 'active' : '' }}" id="menu-collections" title="Collections">
                        <i class="bi bi-collection"></i>
                        <span>Collections</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('seller.products.index') }}" class="sidebar-menu-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" id="menu-products" title="Products">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                </li>
                
            </ul>
        </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
    <div class="sidebar-profile">
        <img src="../assets/images/avatar.png" alt="Administrator" class="sidebar-profile-img"
            onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop'">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{auth()->user()->name}}</div>
            <div class="sidebar-profile-email">{{auth()->user()->email}}</div>
        </div>
    </div>
</div>
