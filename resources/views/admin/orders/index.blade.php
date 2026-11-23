@extends('layouts.admin')

@section('customCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">Review customer orders, item decisions, and payment status.</p>
        </div>
    </div>

    <div class="table-card-custom">
        <div class="table-header-control">
            <form id="order-search-form" class="table-search-box search-form" role="search" autocomplete="off">
                <i class="bi bi-search table-search-icon" aria-hidden="true"></i>
                <input type="search" id="order-search" class="table-search-input"
                    placeholder="Search orders..." aria-label="Search orders">
            </form>

            <div class="table-filter-group">
                <select id="order-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter orders by status">
                    <option value="">All order statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select id="payment-status-filter" class="form-select-custom form-select-inline"
                    aria-label="Filter orders by payment status">
                    <option value="">All payment statuses</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>
                <select id="order-length" class="form-select-custom form-select-inline" aria-label="Rows per page">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <table id="order-table" class="table-custom w-100">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Placed</th>
                    <th>Items</th>
                    <th>Payable</th>
                    <th>Order status</th>
                    <th>Payment</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@section('customjs')
    <script>
        $(function () {
            const $table = $('#order-table');
            const $search = $('#order-search');
            const $status = $('#order-status-filter');
            const $paymentStatus = $('#payment-status-filter');
            const $length = $('#order-length');
            let searchTimer = null;

            const table = $table.DataTable({
                processing: true,
                serverSide: true,
                dom: '<"dt-body"<"table-responsive"t>r><"table-footer-control dt-footer"ip>',
                ajax: {
                    url: @json(route('admin.orders.index')),
                    data: function (request) {
                        request.status = $status.val();
                        request.payment_status = $paymentStatus.val();
                    }
                },
                columns: [
                    { data: 'order', name: 'orders.order_number' },
                    { data: 'customer', name: 'customer', orderable: false },
                    { data: 'created_at', name: 'orders.created_at' },
                    { data: 'items', name: 'items_sum_quantity', searchable: false },
                    { data: 'payable_total', name: 'orders.payable_total', searchable: false },
                    { data: 'status', name: 'orders.status' },
                    { data: 'payment_status', name: 'orders.payment_status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[2, 'desc']],
                pageLength: 10,
                language: {
                    processing: '<span class="dt-processing-box"><span class="dt-spinner" aria-hidden="true"></span><span>Loading...</span></span>',
                    emptyTable: 'No orders have been placed yet.',
                    zeroRecords: 'No orders match your search or filters.',
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

            $('#order-search-form').on('submit', function (event) {
                event.preventDefault();
                clearTimeout(searchTimer);
                applySearch();
            });

            $status.add($paymentStatus).on('change', function () {
                table.draw();
            });

            $length.on('change', function () {
                table.page.len(parseInt(this.value, 10)).draw();
            });
        });
    </script>
@endsection
