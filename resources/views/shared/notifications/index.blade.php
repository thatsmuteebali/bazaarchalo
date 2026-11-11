@extends($panel === 'admin' ? 'layouts.admin' : ($panel === 'seller' ? 'layouts.seller' : 'layouts.app'))

@section('content')
    @if ($panel === 'customer')
        <div class="container py-5">
            <div class="row">
                @include('customer.sidebar')
                <div class="col-lg-8 col-xl-9">
                @else
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Notifications</h1>
                            <p class="page-subtitle">Your latest account and marketplace updates.</p>
                        </div>
                        @if (auth()->user()->unreadNotifications()->exists())
                            <form method="POST" action="{{ route($panel . '.notifications.read-all') }}">
                                @csrf
                                <button class="btn btn-outline-success" type="submit">Mark all as read</button>
                            </form>
                        @endif
                    </div>
                    <div class="card p-3">
    @endif
    @if ($panel === 'customer')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Notifications</h1>
                <p class="text-muted mb-0">Your latest account and marketplace updates.</p>
            </div>
            @if (auth()->user()->unreadNotifications()->exists())
                <form method="POST" action="{{ route($panel . '.notifications.read-all') }}">
                    @csrf
                    <button class="btn btn-outline-success" type="submit">Mark all as read</button>
                </form>
            @endif
        </div>
    @endif

    <div class="list-group mb-4">
        @forelse ($notifications as $notification)
            <form method="POST" action="{{ route($panel . '.notifications.read', $notification->id) }}">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action text-start
                                    {{ $notification->read_at ? '' : 'bg-light' }}">
                    <span class="d-flex align-items-start gap-3">
                        <i class="{{ $notification->data['icon'] ?? 'bi bi-bell' }} fs-4 text-success"></i>
                        <span class="flex-grow-1">
                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                            <span class="d-block">{{ $notification->data['message'] ?? '' }}</span>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </span>
                        @unless ($notification->read_at)
                            <span class="badge text-bg-success">Unread</span>
                        @endunless
                    </span>
                </button>
            </form>
        @empty
            <div class="list-group-item text-muted">You have no notifications yet.</div>
        @endforelse
    </div>
    {{ $notifications->links('pagination::bootstrap-5') }}
    @if ($panel === 'customer')
        </div>
        </div>
        </div>
    @else
        </div>
    @endif
@endsection
