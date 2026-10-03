<div class="col-lg-4 col-xl-3">
    <div class="account-sidebar bg-light rounded p-4">
        <div class="text-center border-bottom pb-4 mb-3">
            <div class="account-avatar rounded-circle bg-secondary mx-auto mb-3"><i class="fas fa-user text-white"></i>
            </div>
            <h4 class="mb-1">Sarah Johnson</h4>
            <p class="account-email text-muted mb-0">sarah.johnson@example.com</p>
        </div>
        <div class="nav flex-column nav-pills account-tabs" id="account-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link text-start {{ request()->routeIs('customer.account') ? 'active' : '' }}"
                href="{{ route('customer.account') }}"><i class="fas fa-th-large"></i>My
                Account</a>
            <a class="nav-link text-start {{ request()->routeIs('customer.profile') ? 'active' : '' }}"
                href="{{ route('customer.profile') }}"><i class="fas fa-user"></i>Profile</a>
            <a class="nav-link text-start {{ request()->routeIs('customer.orders') ? 'active' : '' }}"
                href="{{ route('customer.orders') }}"><i class="fas fa-box"></i>My Orders <span
                    class="account-tab-count">3</span></a>
            <a class="nav-link text-start {{ request()->routeIs('customer.wishlist') ? 'active' : '' }}"
                href="{{ route('customer.wishlist') }}"><i class="fas fa-heart"></i>Wishlist <span
                    class="account-tab-count">4</span></a>
            <a class="nav-link text-start {{ request()->routeIs('customer.addresses') ? 'active' : '' }}" href="{{ route('customer.addresses') }}"><i class="fas fa-map-marker-alt"></i>Addresses</a>
            <a href="{{ route('logout') }}" class="nav-link text-start text-danger mt-3"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                    class="fas fa-sign-out-alt"></i>Sign out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
