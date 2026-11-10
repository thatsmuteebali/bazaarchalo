@extends('layouts.admin')

@section('customCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Products List</h1>
            <p class="page-subtitle">Review products and manage their status and featured visibility.</p>
        </div>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form id="product-search-form" class="table-search-box search-form" role="search" autocomplete="off">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" id="product-search" class="table-search-input"
                    placeholder="Search products..." aria-label="Search products">
            </form>

            <div class="table-filter-group">
                <select id="product-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter products by status">
                    <option value="">All statuses</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select id="product-length" class="form-select-custom form-select-inline" aria-label="Rows per page">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <table id="product-table" class="table-custom w-100">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Seller</th>
                    <th>Shop</th>
                    <th>Collection</th>
                    <th>Category</th>
                    <th>Slug</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@section('customjs')
    <script>
        $(function () {
            const $table = $('#product-table');
            const $search = $('#product-search');
            const $status = $('#product-status-filter');
            const $length = $('#product-length');
            let searchTimer = null;

            const table = $table.DataTable({
                processing: true,
                serverSide: true,
                dom: '<"dt-body"<"table-responsive"t>r><"table-footer-control dt-footer"ip>',
                ajax: {
                    url: @json(route('admin.products.index')),
                    data: function (request) {
                        request.status = $status.val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'products.id', orderable: true, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'products.name' },
                    { data: 'seller_name', name: 'seller.name' },
                    { data: 'shop_name', name: 'shop.name' },
                    { data: 'collection_name', name: 'collection.name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'slug', name: 'products.slug' },
                    { data: 'price', name: 'price', searchable: false },
                    { data: 'stock', name: 'products.stock', searchable: false },
                    { data: 'status', name: 'products.status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    processing: '<span class="dt-processing-box"><span class="dt-spinner" aria-hidden="true"></span><span>Loading...</span></span>',
                    emptyTable: 'No products have been added yet.',
                    zeroRecords: 'No products match your search or filters.',
                    info: 'Showing _START_ to _END_ of _TOTAL_ results',
                    infoEmpty: 'Showing 0 results',
                    infoFiltered: '',
                    paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
                }
            });

            $table.on('processing.dt', function (e, settings, isProcessing) {
                $table.closest('.dt-body').toggleClass('is-loading', isProcessing);
            });

            function applySearch() {
                const value = $search.val().trim();
                if (table.search() !== value) {
                    table.search(value).draw();
                }
            }

            $search.on('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(applySearch, 400);
            });

            $('#product-search-form').on('submit', function (event) {
                event.preventDefault();
                clearTimeout(searchTimer);
                applySearch();
            });

            $status.on('change', function () {
                table.draw();
            });

            $length.on('change', function () {
                table.page.len(parseInt(this.value, 10)).draw();
            });
        });
    </script>
@endsection
