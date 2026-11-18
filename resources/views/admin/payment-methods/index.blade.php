@extends('layouts.admin')
@section('customCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Payment Methods</h1>
            <p class="page-subtitle">Manage the payment options customers can choose at checkout.</p>
        </div>
        <a href="{{ route('admin.payment-methods.create') }}" class="btn-quick-action">
            <i class="bi bi-plus-lg"></i>
            <span>Add Payment Method</span>
        </a>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form id="payment-method-search-form" class="table-search-box search-form" role="search" autocomplete="off">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" id="payment-method-search" class="table-search-input"
                    placeholder="Search payment methods..." aria-label="Search payment methods">
            </form>

            <div class="table-filter-group">
                <select id="payment-method-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter payment methods by status">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="trashed">Trashed</option>
                </select>
                <select id="payment-method-length" class="form-select-custom form-select-inline"
                    aria-label="Rows per page">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <table id="payment-method-table" class="table-custom w-100">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Name</th>
                    <th>Slug</th>
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
            const $table = $('#payment-method-table');
            const $search = $('#payment-method-search');
            const $status = $('#payment-method-status-filter');
            const $length = $('#payment-method-length');
            let searchTimer = null;

            const table = $table.DataTable({
                processing: true,
                serverSide: true,
                dom: '<"dt-body"<"table-responsive"t>r><"table-footer-control dt-footer"ip>',
                ajax: {
                    url: @json(route('admin.payment-methods.list')),
                    data: function (request) {
                        request.status = $status.val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'payment_methods.id', orderable: true, searchable: false },
                    { data: 'name', name: 'payment_methods.name' },
                    { data: 'slug', name: 'payment_methods.slug' },
                    { data: 'status', name: 'payment_methods.status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    processing: '<span class="dt-processing-box"><span class="dt-spinner" aria-hidden="true"></span><span>Loading...</span></span>',
                    emptyTable: 'No payment methods have been added yet.',
                    zeroRecords: 'No payment methods match your search or filters.',
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

            $('#payment-method-search-form').on('submit', function (event) {
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
