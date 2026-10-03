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
                    <a href="{{route('admin.dashboard')}}" class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="menu-overview" title="Dashboard">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{route('admin.categories.list')}}" class="sidebar-menu-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" id="menu-overview" title="Categories">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Categories</span>
                    </a>
                </li>
                {{-- <li class="sidebar-menu-item">
                    <a href="dashboard.html" class="sidebar-menu-link" id="menu-overview" title="Overview">
                        <i class="bi bi-cart-check-fill"></i>
                        <span>Orders</span>
                    </a>
                </li> --}}
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
