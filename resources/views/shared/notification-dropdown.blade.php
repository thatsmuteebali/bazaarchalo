@php
    $notifications = auth()->user()->notifications()->latest()->limit(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="dropdown">
    <button class="navbar-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
        aria-expanded="false" id="btn-notifications" data-bs-auto-close="outside"
        aria-label="Notifications{{ $unreadCount ? ', '.$unreadCount.' unread' : '' }}">
        <i class="bi bi-bell"></i>
        @if ($unreadCount)
            <span class="navbar-action-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-notification p-0"
        aria-labelledby="btn-notifications">
        <div class="notification-header">
            <h6 class="notification-title">Notifications</h6>
            @if ($unreadCount)
                <form method="POST" action="{{ route($routePrefix.'.notifications.read-all') }}">
                    @csrf
                    <button class="btn-clear-all" type="submit">Mark all read</button>
                </form>
            @endif
        </div>
        <div class="notification-list">
            @forelse ($notifications as $notification)
                <form method="POST" action="{{ route($routePrefix.'.notifications.read', $notification->id) }}">
                    @csrf
                    <button class="notification-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}"
                        type="submit">
                        <span class="notification-icon bg-primary text-white">
                            <i class="{{ $notification->data['icon'] ?? 'bi bi-bell' }}"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-text"><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong><br>
                                {{ $notification->data['message'] ?? '' }}</span>
                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        @unless ($notification->read_at)
                            <span class="notification-unread-dot" aria-label="Unread"></span>
                        @endunless
                    </button>
                </form>
            @empty
                <p class="notification-empty">You're all caught up.</p>
            @endforelse
        </div>
        <a href="{{ route($routePrefix.'.notifications.index') }}" class="notification-footer">View all notifications</a>
    </div>
</div>
