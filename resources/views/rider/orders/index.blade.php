@extends('layouts.rider')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Assigned orders</h1>
            <p class="page-subtitle">View your deliveries and update pickup or delivery progress.</p>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="card border-light shadow-sm p-5 text-center">
            <div class="mb-3">
                <i class="bi bi-receipt text-muted" style="font-size: 2.5rem;" aria-hidden="true"></i>
            </div>
            <h2 class="h5">No assigned orders yet</h2>
            <p class="text-muted mb-0">Orders assigned to your rider account will appear here.</p>
        </div>
    @else
        <div class="card border-light shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Order</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Items</th>
                            <th scope="col">Status</th>
                            <th scope="col">Placed</th>
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>{{ $order->customer?->name ?? $order->customer_name }}</td>
                                <td>{{ $order->items->count() }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('rider.orders.show', $order) }}">View order</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $orders->links() }}</div>
        </div>
    @endif
@endsection
