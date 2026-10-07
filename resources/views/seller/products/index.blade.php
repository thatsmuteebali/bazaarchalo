@extends('layouts.seller')

@section('content')
    @php
        $search = (string) request('q', '');
        $statusFilter = (string) request('status', '');
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Products</h1>
            <p class="page-subtitle">Manage the products of your shops.</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn-quick-action">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Add Product</span>
        </a>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form method="GET" action="{{ route('seller.products.index') }}" class="table-search-box search-form">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" name="q" class="table-search-input" value="{{ $search }}"
                    placeholder="Search products..." aria-label="Search products">

                <select name="status" class="form-select-custom" style="width:auto;" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    @foreach (['active', 'draft', 'inactive'] as $s)
                        <option value="{{ $s }}" @selected($statusFilter === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>

                @if ($search !== '' || $statusFilter !== '')
                    <a href="{{ route('seller.products.index') }}" class="table-btn-action" title="Clear filters"
                        aria-label="Clear filters">
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
                        <th>Product</th>
                        <th>Shop</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $status_label = match ($product->status) {
                                'active' => 'success',
                                'inactive' => 'failed',
                                default => 'pending',
                            };
                            $queryParams = [
                                'q' => $search,
                                'status' => $statusFilter,
                                'page' => $products->currentPage(),
                                'per_page' => $products->perPage(),
                            ];
                        @endphp
                        <tr>
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="table-user-cell">
                                    @if ($product->coverImage)
                                        <img src="{{ $product->coverImage->url }}" alt="{{ $product->name }}"
                                            class="table-user-avatar"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/images/avatar.png') }}'">
                                    @else
                                        <span
                                            class="d-inline-flex align-items-center justify-content-center bg-light text-muted"
                                            style="width: 56px; height: 44px;">
                                            <i class="bi bi-image" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div class="table-user-name">
                                            {{ $product->name }}
                                            @if ($product->is_featured)
                                                <i class="bi bi-star-fill text-warning" title="Featured"></i>
                                            @endif
                                        </div>
                                        @if ($product->has_variants)
                                            <div class="table-user-sub">{{ $product->variants_count }} variants</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->shop->name ?? '—' }}</td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>
                                @if ($product->has_variants)
                                    {{ number_format($product->variants()->min('price'), 2) }}
                                @else
                                    {{ number_format($product->price, 2) }}
                                @endif
                            </td>
                            <td>
                                @if ($product->stock < 1)
                                    <span class="badge-table failed">Out of stock</span>
                                @else
                                    {{ $product->stock }}
                                @endif
                            </td>
                            <td><span class="badge-table {{ $status_label }}">{{ ucfirst($product->status) }}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('seller.products.show', ['product' => $product]) }}"
                                        class="table-btn-action" title="View product"
                                        aria-label="View {{ $product->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.products.edit', ['product' => $product]) }}"
                                        class="table-btn-action" title="Edit product"
                                        aria-label="Edit {{ $product->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('seller.products.destroy', ['product' => $product] + $queryParams) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="table-btn-action delete" title="Delete product"
                                            aria-label="Delete {{ $product->name }}" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal" data-confirm-delete
                                            data-item-name="{{ $product->name }}" data-item-type="product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ $search !== '' || $statusFilter !== '' ? 'No products match your filters.' : 'You have not added any products yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer-control">
            {{ $products->links() }}
        </div>
    </div>
@endsection
