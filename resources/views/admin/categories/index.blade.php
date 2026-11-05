@extends('layouts.admin')
@section('customCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
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

    <div class="table-card-custom">
        <!-- Header Controls -->
        <div class="table-header-control">
            <form id="category-search-form" class="table-search-box search-form" role="search" autocomplete="off">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" id="category-search" class="table-search-input"
                    placeholder="Search categories..." aria-label="Search categories">
            </form>

            <div class="table-filter-group">
                <select id="category-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter categories by status">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="trashed">Trashed</option>
                </select>
                <select id="category-length" class="form-select-custom form-select-inline"
                    aria-label="Rows per page">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <!-- DataTable renders the table, info and pagination here -->
        <table id="category-table" class="table-custom w-100">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Image</th>
                    <th>Category</th>
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
            const $table = $('#category-table');
            const $search = $('#category-search');
            const $status = $('#category-status-filter');
            const $length = $('#category-length');
            let searchTimer = null;

            const table = $table.DataTable({
                processing: true,
                serverSide: true,
                dom: '<"dt-body"<"table-responsive"t>r><"table-footer-control dt-footer"ip>',
                ajax: {
                    url: @json(route('admin.categories.list')),
                    data: function (request) {
                        request.status = $status.val();
                    }
                },
                columns: [
                    // Sorts by the real id on the server; the id itself is never sent to the browser
                    { data: 'DT_RowIndex', name: 'categories.id', orderable: true, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'categories.name' },
                    { data: 'status', name: 'categories.status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],   // newest first
                pageLength: 10,
                language: {
                    processing: '<span class="dt-processing-box"><span class="dt-spinner" aria-hidden="true"></span><span>Loading...</span></span>',
                    emptyTable: 'No categories have been added yet.',
                    zeroRecords: 'No categories match your search or filters.',
                    info: 'Showing _START_ to _END_ of _TOTAL_ results',
                    infoEmpty: 'Showing 0 results',
                    infoFiltered: '',
                    paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
                }
            });

            // Dim the rows while a request is running
            $table.on('processing.dt', function (e, settings, isProcessing) {
                $table.closest('.dt-body').toggleClass('is-loading', isProcessing);
            });

            // Search (debounced while typing, instant on Enter)
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

            $('#category-search-form').on('submit', function (event) {
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
