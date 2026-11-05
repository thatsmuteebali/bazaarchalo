@extends('layouts.admin')

@section('customCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $title }} List</h1>
            <p class="page-subtitle">Manage {{ strtolower($title) }} and their account status.</p>
        </div>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form id="managed-user-search-form" class="table-search-box search-form" role="search" autocomplete="off">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" id="managed-user-search" class="table-search-input"
                    placeholder="Search {{ strtolower($title) }}..." aria-label="Search {{ strtolower($title) }}">
            </form>

            <div class="table-filter-group">
                <select id="managed-user-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter {{ strtolower($title) }} by status">
                    <option value="">All statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <select id="managed-user-length" class="form-select-custom form-select-inline" aria-label="Rows per page">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <table id="managed-user-table" class="table-custom w-100">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
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
            const $table = $('#managed-user-table');
            const $search = $('#managed-user-search');
            const $status = $('#managed-user-status-filter');
            const $length = $('#managed-user-length');
            let searchTimer = null;

            const table = $table.DataTable({
                processing: true,
                serverSide: true,
                dom: '<"dt-body"<"table-responsive"t>r><"table-footer-control dt-footer"ip>',
                ajax: {
                    url: @json(route($routeName)),
                    data: function (request) {
                        request.status = $status.val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'users.id', orderable: true, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'users.name' },
                    { data: 'username', name: 'users.username' },
                    { data: 'email', name: 'users.email' },
                    { data: 'phone', name: 'users.phone' },
                    { data: 'status', name: 'users.status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    processing: '<span class="dt-processing-box"><span class="dt-spinner" aria-hidden="true"></span><span>Loading...</span></span>',
                    emptyTable: 'No {{ strtolower($title) }} have registered yet.',
                    zeroRecords: 'No {{ strtolower($title) }} match your search or filters.',
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

            $('#managed-user-search-form').on('submit', function (event) {
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
