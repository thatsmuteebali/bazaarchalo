<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderDispatchService;
use App\Services\RiderPaymentCollectionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PanelController extends Controller
{
    public function dashboard()
    {
        return view('rider.dashboard');
    }

    public function orders(Request $request)
    {
        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $query = Order::query()
            ->where('rider_id', auth()->id())
            ->select('orders.*')
            ->with('customer:id,name')
            ->withCount('items')
            ->withSum('items', 'quantity');

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status, $paymentStatus) {
                    if (in_array($status, [
                        Order::STATUS_PENDING,
                        Order::STATUS_PROCESSING,
                        Order::STATUS_COMPLETED,
                        Order::STATUS_CANCELLED,
                    ], true)) {
                        $query->where('orders.status', $status);
                    }

                    if (in_array($paymentStatus, [
                        Order::PAYMENT_UNPAID,
                        Order::PAYMENT_PAID,
                        Order::PAYMENT_FAILED,
                        Order::PAYMENT_REFUNDED,
                    ], true)) {
                        $query->where('orders.payment_status', $paymentStatus);
                    }
                }, true)
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('orders.customer_name', 'like', "%{$keyword}%")
                            ->orWhere('orders.customer_phone', 'like', "%{$keyword}%")
                            ->orWhere('orders.customer_email', 'like', "%{$keyword}%")
                            ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$keyword}%"));
                    });
                })
                ->addColumn('order', function (Order $order) {
                    $lineLabel = $order->items_count === 1 ? 'line' : 'lines';

                    return '<div class="table-user-name">'.e($order->order_number).'</div>'
                        .'<div class="table-user-sub">'.$order->items_count.' product '.$lineLabel.'</div>';
                })
                ->addColumn('customer', function (Order $order) {
                    return '<div class="table-user-name">'.e($order->customer?->name ?? $order->customer_name).'</div>'
                        .'<div class="table-user-sub">'.e($order->customer_phone).'</div>';
                })
                ->addColumn('items', function (Order $order) {
                    $quantity = (int) ($order->items_sum_quantity ?? 0);

                    return $quantity.' '.($quantity === 1 ? 'unit' : 'units');
                })
                ->editColumn('payable_total', fn (Order $order) => \App\Support\Money::format($order->payable_total))
                ->editColumn('payment_status', function (Order $order) {
                    $statusClass = match ($order->payment_status) {
                        Order::PAYMENT_PAID => 'success',
                        Order::PAYMENT_FAILED, Order::PAYMENT_REFUNDED => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($order->payment_status)).'</span>';
                })
                ->editColumn('status', function (Order $order) {
                    $statusClass = match ($order->status) {
                        Order::STATUS_PROCESSING, Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_CANCELLED => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($order->status)).'</span>';
                })
                ->editColumn('created_at', function (Order $order) {
                    return $order->created_at->format('M j, Y')
                        .'<small class="d-block text-muted">'.$order->created_at->format('g:i A').'</small>';
                })
                ->addColumn('actions', function (Order $order) {
                    $url = e(route('rider.orders.show', $order));
                    $orderNumber = e($order->order_number);

                    return '<div class="d-flex justify-content-center">'
                        .'<a href="'.$url.'" class="table-btn-action" title="View order" aria-label="View order '
                        .$orderNumber.'"><i class="bi bi-eye" aria-hidden="true"></i></a></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['order', 'customer', 'payment_status', 'status', 'created_at', 'actions'])
                ->toJson();
        }

        return view('rider.orders.index');
    }

    public function show(Order $order)
    {
        abort_unless((int) $order->rider_id === (int) auth()->id(), 404);

        $order->load([
            'customer:id,name,email,phone',
            'items' => fn ($query) => $query
                ->whereIn('status', [
                    OrderItem::STATUS_ACCEPTED,
                    OrderItem::STATUS_SHIPPED,
                    OrderItem::STATUS_DELIVERED,
                ])
                ->orderBy('id'),
            'tracking' => fn ($query) => $query
                ->where(function ($query) {
                    $query->whereNull('order_item_id')
                        ->orWhereHas('item', fn ($itemQuery) => $itemQuery->whereIn('status', [
                            OrderItem::STATUS_ACCEPTED,
                            OrderItem::STATUS_SHIPPED,
                            OrderItem::STATUS_DELIVERED,
                        ]));
                })
                ->with(['item:id,product_name', 'creator:id,name'])
                ->reorder()
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        $deliverableItems = $order->items->whereNotIn('status', [
            OrderItem::STATUS_REJECTED,
            OrderItem::STATUS_CANCELLED,
        ]);
        $canMarkPaid = $order->isCashOnDelivery()
            && $order->payment_status === Order::PAYMENT_UNPAID
            && $deliverableItems->isNotEmpty()
            && $deliverableItems->every(fn (OrderItem $item) => $item->status === OrderItem::STATUS_DELIVERED);

        return view('rider.orders.show', compact('order', 'canMarkPaid'));
    }

    public function updateItemStatus(
        Request $request,
        Order $order,
        OrderItem $orderItem,
        OrderDispatchService $dispatch,
    ) {
        abort_unless((int) $orderItem->order_id === (int) $order->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in([OrderItem::STATUS_SHIPPED, OrderItem::STATUS_DELIVERED])],
        ]);

        $dispatch->updateItemStatus($order, $orderItem, $data['status'], $request->user());

        return redirect()
            ->route('rider.orders.show', $order)
            ->with('success', $data['status'] === OrderItem::STATUS_SHIPPED
                ? 'The item has been marked as picked up.'
                : 'The item has been marked as delivered.');
    }

    public function markOrderPaid(
        Order $order,
        RiderPaymentCollectionService $payments,
        Request $request,
    ) {
        $payments->collectCash($order, $request->user());

        return redirect()
            ->route('rider.orders.show', $order)
            ->with('success', 'Cash received and order payment marked as paid.');
    }
}
