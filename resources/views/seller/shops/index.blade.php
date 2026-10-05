@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">My Shops</h1>
            <p class="page-subtitle">Manage your shop details and visibility.</p>
        </div>
        <a href="{{ route('seller.shops.create') }}" class="btn-quick-action">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Add Shop</span>
        </a>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form method="GET" action="{{ route('seller.shops.index') }}" class="table-search-box search-form">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" name="search" class="table-search-input" value="{{ $search }}"
                    placeholder="Search shops..." aria-label="Search shops">
                @if ($search !== '')
                    <a href="{{ route('seller.shops.index') }}" class="table-btn-action" title="Clear search" aria-label="Clear search">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
                <button type="submit" class="btn-quick-action">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <span>Search</span>
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Shop</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shops as $shop)
                        <tr>
                            <td>{{ $shops->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="table-user-cell">
                                    @if ($shop->banner)
                                    <img src="{{ asset('storage/' . $shop->banner) }}" alt="{{ $shop->name }}" class="table-user-avatar"
                                        onerror="this.src='../assets/images/avatar.png'">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted"
                                            style="width: 56px; height: 44px;">
                                            <i class="bi bi-shop" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div class="table-user-name">{{ $shop->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $shop->address ?: '—' }}</td>
                            @php
                                $status_label = match ($shop->status) {
                                    'active' => 'success',
                                    'inactive' => 'failed',
                                    default => 'pending',
                                };
                            @endphp
                            <td><span class="badge-table {{ $status_label }}">{{ucfirst($shop->status)}}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('seller.shops.show', $shop) }}" class="table-btn-action" title="View shop" aria-label="View {{ $shop->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.shops.edit', $shop) }}" class="table-btn-action" title="Edit shop" aria-label="Edit {{ $shop->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('seller.shops.destroy', ['shop' => $shop, 'search' => $search, 'status' => $status, 'page' => $shops->currentPage()]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="table-btn-action delete" title="Delete shop" aria-label="Delete {{ $shop->name }}"
                                            data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete
                                            data-item-name="{{ $shop->name }}" data-item-type="shop">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                {{ $search !== '' || $status !== '' ? 'No shops match these filters.' : 'You have not added any shops yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer-control">
            {{ $shops->links() }}
        </div>
    </div>

@endsection
