<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderDispatchService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PanelController extends Controller
{
    public function dashboard()
    {
        return view('rider.dashboard');
    }

    public function orders()
    {
        $orders = Order::query()
            ->where('rider_id', auth()->id())
            ->with(['customer:id,name', 'items'])
            ->latest()
            ->paginate(15);

        return view('rider.orders.index', compact('orders'));
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

        return view('rider.orders.show', compact('order'));
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
}
