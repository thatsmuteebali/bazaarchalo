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

    <!-- START: Basic Table Card Container -->
    <div class="table-card-custom">
        <!-- Header Controls -->
        <div class="table-header-control">
            <label for="category-status-filter" class="form-label-custom mb-0">Filter by status</label>
            <select id="category-status-filter" class="form-select-custom" aria-label="Filter categories by status">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <!-- Responsive Table Wrapper -->
        <div class="table-responsive">
            <table id="category-table" class="table-custom table w-100">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                        <th class="d-none">Sort</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- END: Basic Table Card Container -->
@endsection

@section('customjs')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            const table = $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: @json(route('admin.categories.list')),
                    data: function (request) {
                        request.status = $('#category-status-filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'categories.name' },
                    { data: 'status', name: 'categories.status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                    { data: 'sort_id', name: 'categories.id', visible: false, searchable: false }
                ],
                order: [[5, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                stateSaveParams: function (settings, data) {
                    data.categoryStatus = $('#category-status-filter').val();
                },
                stateLoadParams: function (settings, data) {
                    $('#category-status-filter').val(data.categoryStatus || '');
                },
                language: {
                    emptyTable: 'No categories have been added yet.',
                    zeroRecords: 'No categories match your search or filters.'
                }
            });

            $('#category-status-filter').on('change', function () {
                table.draw();
            });
        });
    </script>
@endsection
