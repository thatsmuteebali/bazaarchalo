@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">My Collections</h1>
            <p class="page-subtitle">Manage your seasonal and promotional collections.</p>
        </div>
        <a href="{{ route('seller.collections.create') }}" class="btn-quick-action">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Add Collection</span>
        </a>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form method="GET" action="{{ route('seller.collections.index') }}" class="table-search-box search-form">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" name="search" class="table-search-input" value="{{ $search }}"
                    placeholder="Search collections..." aria-label="Search collections">
                @if ($search !== '')
                    <a href="{{ route('seller.collections.index') }}" class="table-btn-action" title="Clear search" aria-label="Clear search">
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
                        <th>Collection</th>
                        <th>Statue</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($collections as $collection)
                        <tr>
                            <td>{{ $collections->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($collection->image)
                                        <img src="{{ asset('storage/' . $collection->image) }}" alt="{{ $collection->name }}"
                                            style="width: 56px; height: 44px; object-fit: cover;">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted"
                                            style="width: 56px; height: 44px;">
                                            <i class="bi bi-collection" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div>{{ $collection->name }}</div>
                                    </div>
                                </div>
                            </td>
                            @php
                                $status_label = match ($collection->status) {
                                    'active' => 'success',
                                    'inactive' => 'failed',
                                    default => 'pending',
                                };
                            @endphp
                            <td><span class="badge-table {{ $status_label }}">{{ucfirst($collection->status)}}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('seller.collections.show', ['collection' => $collection, 'search' => $search, 'page' => $collections->currentPage()]) }}"
                                        class="table-btn-action" title="View collection" aria-label="View {{ $collection->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.collections.edit', ['collection' => $collection, 'search' => $search, 'page' => $collections->currentPage()]) }}"
                                        class="table-btn-action" title="Edit collection" aria-label="Edit {{ $collection->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('seller.collections.destroy', ['collection' => $collection, 'search' => $search, 'page' => $collections->currentPage()]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="table-btn-action delete" title="Delete collection"
                                            aria-label="Delete {{ $collection->name }}" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal" data-confirm-delete
                                            data-item-name="{{ $collection->name }}" data-item-type="collection">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                {{ $search !== '' ? 'No collections match your search.' : 'You have not added any collections yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer-control">
            {{ $collections->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
