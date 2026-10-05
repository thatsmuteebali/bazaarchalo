@extends('layouts.admin')
@section('customCss')
@endsection

@section('content')
    <!-- START: Page Header Banner -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Categories List</h1>
            <p class="page-subtitle">Product Categories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-quick-action">
            <i class="bi bi-plus-lg"></i>
            <span>Add Category</span>
        </a>
    </div>
    <!-- END: Page Header Banner -->

    <!-- START: Basic Table Card Container -->
    <div class="table-card-custom">
        <!-- Header Controls -->
        <div class="table-header-control">
            <form method="GET" action="{{ route('admin.categories.list') }}" class="table-search-box search-form">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" name="search" class="table-search-input" value="{{ $search }}"
                    placeholder="Search categories..." aria-label="Search categories">
                @if ($search !== '')
                    <a href="{{ route('admin.categories.list') }}" class="table-btn-action" title="Clear search"
                        aria-label="Clear search">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
                <button type="submit" class="btn-quick-action">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <span>Search</span>
                </button>
            </form>

        </div>

        <!-- Responsive Table Wrapper -->
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="table-user-cell">
                                    @if ($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="table-user-avatar"
                                        onerror="this.src='../assets/images/avatar.png'">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted"
                                            style="width: 56px; height: 44px;">
                                            <i class="bi bi-category" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $category->name }}</td>
                            @php
                                $status_label = match ($category->status) {
                                    'active' => 'success',
                                    'inactive' => 'failed',
                                    default => 'pending',
                                };
                            @endphp
                            <td><span class="badge-table {{ $status_label }}">{{ucfirst($category->status)}}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="table-btn-action" title="View category" aria-label="View {{ $category->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', ['category' => $category, 'search' => $search, 'page' => $categories->currentPage()]) }}"
                                        class="table-btn-action" title="Edit category"
                                        aria-label="Edit {{ $category->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.categories.destroy', ['category' => $category, 'search' => $search, 'page' => $categories->currentPage()]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="table-btn-action delete" title="Delete category"
                                            aria-label="Delete {{ $category->name }}" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal" data-confirm-delete
                                            data-item-name="{{ $category->name }}" data-item-type="category">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                {{ $search !== '' ? 'No categories match your search.' : 'No categories have been added yet.' }}
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Footer Controls / Pagination -->
        <div class="table-footer-control">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- END: Basic Table Card Container -->
@endsection
