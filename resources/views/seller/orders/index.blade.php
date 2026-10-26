@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">Review orders that include your products.</p>
        </div>
    </div>

    <div class="table-card-custom">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Placed</th>
                        <th>Your items</th>
                        <th>Your subtotal</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $itemStatuses = $order->items->pluck('status')->unique();
                            $sellerStatus = $itemStatuses->contains('pending')
                                ? 'needs action'
                                : ($itemStatuses->count() === 1 ? $itemStatuses->first() : 'mixed');
                            $statusClass = match ($sellerStatus) {
                                'accepted', 'delivered' => 'success',
                                'rejected', 'cancelled' => 'failed',
                                default => 'pending',
                            };
                            $quantity = (int) ($order->seller_items_quantity ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <div class="table-user-name">{{ $order->order_number }}</div>
                                <div class="table-user-sub">{{ $order->seller_items_count }} product {{ $order->seller_items_count === 1 ? 'line' : 'lines' }}</div>
                            </td>
                            <td>
                                <div class="table-user-name">{{ $order->customer?->name ?? $order->customer_name }}</div>
                                <div class="table-user-sub">{{ $order->customer_phone }}</div>
                            </td>
                            <td>{{ $order->created_at->format('M j, Y') }}<small class="d-block text-muted">{{ $order->created_at->format('g:i A') }}</small></td>
                            <td>{{ $quantity }} {{ $quantity === 1 ? 'unit' : 'units' }}</td>
                            <td>{{ \App\Support\Money::format($order->seller_items_sum_line_total) }}</td>
                            <td><span class="badge-table {{ $statusClass }}">{{ ucfirst($sellerStatus) }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('seller.orders.show', $order) }}" class="table-btn-action"
                                    title="View order" aria-label="View order {{ $order->order_number }}">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt d-block mb-2" style="font-size: 28px" aria-hidden="true"></i>
                                No orders contain your products yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer-control">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
