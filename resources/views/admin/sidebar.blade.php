<div class="sidebar-wrapper" id="sidebar">

    {{-- @php
        $catalogActive = request()->routeIs('admin.categories*');
    @endphp --}}
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
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        id="menu-overview" title="Dashboard">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.categories.list') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
                        id="menu-overview" title="Categories">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Categories</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.customers.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                        id="menu-customers" title="Customers">
                        <i class="bi bi-person-fill"></i>
                        <span>Customers</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.sellers.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}"
                        id="menu-sellers" title="Sellers">
                        <i class="bi bi-shop-window"></i>
                        <span>Sellers</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.shops.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}"
                        id="menu-shops" title="Shops">
                        <i class="bi bi-shop-window"></i>
                        <span>Shops</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.collections.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}"
                        id="menu-collections" title="Collections">
                        <i class="bi bi-collection"></i>
                        <span>Collections</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.products.index') }}"
                        class="sidebar-menu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                        id="menu-products" title="Products">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                </li>

                {{-- <li class="sidebar-menu-item">
                    <a href="#menu-catalog" data-bs-toggle="collapse" role="button"
                        class="sidebar-menu-link {{ $catalogActive ? 'active' : '' }}"
                        aria-expanded="{{ $catalogActive ? 'true' : 'false' }}" aria-controls="menu-catalog"
                        title="Catalog">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Catalog</span>
                        <i class="bi bi-chevron-down dropdown-caret"></i>
                    </a>
                    <div class="collapse {{ $catalogActive ? 'show' : '' }}" id="menu-catalog">
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('admin.categories.list') }}"
                                    class="sidebar-submenu-link {{ request()->routeIs('admin.categories.list', 'admin.categories.show', 'admin.categories.edit') ? 'active' : '' }}">
                                    Categories
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.categories.create') }}"
                                    class="sidebar-submenu-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                                    Add Category
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-submenu-link">Products</a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-submenu-link">Brands</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

            </ul>
        </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
    <div class="sidebar-profile">
        <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('assets/images/no_image.png') }}"
            alt="{{ auth()->user()->name }}" class="sidebar-profile-img"
            onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop'">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-profile-email">{{ auth()->user()->email }}</div>
        </div>
    </div>
</div>
