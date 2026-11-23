<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = auth()->id();
        $sellerItems = fn ($query) => $query->where('seller_id', $sellerId);

        $query = Order::query()
            ->whereHas('items', $sellerItems)
            ->select('orders.*')
            ->with([
                'customer:id,name,phone,email',
                'items' => fn ($query) => $sellerItems($query)->select(['id', 'order_id', 'status']),
            ])
            ->withCount(['items as seller_items_count' => $sellerItems])
            ->withSum(['items as seller_items_quantity' => $sellerItems], 'quantity')
            ->withSum(['items as seller_items_sum_line_total' => $sellerItems], 'line_total');

        if ($request->ajax()) {
            return DataTables::eloquent($query)
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('orders.customer_name', 'like', "%{$keyword}%")
                            ->orWhere('orders.customer_phone', 'like', "%{$keyword}%")
                            ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$keyword}%"));
                    });
                })
                ->addColumn('order', function (Order $order) {
                    $lineLabel = $order->seller_items_count === 1 ? 'line' : 'lines';

                    return '<div class="table-user-name">'.e($order->order_number).'</div>'
                        .'<div class="table-user-sub">'.$order->seller_items_count.' product '.$lineLabel.'</div>';
                })
                ->addColumn('customer', function (Order $order) {
                    return '<div class="table-user-name">'.e($order->customer?->name ?? $order->customer_name).'</div>'
                        .'<div class="table-user-sub">'.e($order->customer_phone).'</div>';
                })
                ->editColumn('created_at', function (Order $order) {
                    return $order->created_at->format('M j, Y')
                        .'<small class="d-block text-muted">'.$order->created_at->format('g:i A').'</small>';
                })
                ->addColumn('items', function (Order $order) {
                    $quantity = (int) ($order->seller_items_quantity ?? 0);

                    return $quantity.' '.($quantity === 1 ? 'unit' : 'units');
                })
                ->editColumn('seller_items_sum_line_total', fn (Order $order) => \App\Support\Money::format($order->seller_items_sum_line_total))
                ->addColumn('seller_status', function (Order $order) {
                    $itemStatuses = $order->items->pluck('status')->unique();
                    $sellerStatus = $itemStatuses->contains('pending')
                        ? 'needs action'
                        : ($itemStatuses->count() === 1 ? $itemStatuses->first() : 'mixed');
                    $statusClass = match ($sellerStatus) {
                        'accepted', 'delivered' => 'success',
                        'rejected', 'cancelled' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($sellerStatus)).'</span>';
                })
                ->addColumn('actions', function (Order $order) {
                    $url = e(route('seller.orders.show', $order));
                    $orderNumber = e($order->order_number);

                    return '<a href="'.$url.'" class="table-btn-action" title="View order" aria-label="View order '
                        .$orderNumber.'"><i class="bi bi-eye" aria-hidden="true"></i></a>';
                })
                ->rawColumns(['order', 'customer', 'created_at', 'seller_status', 'actions'])
                ->toJson();
        }

        return view('seller.orders.index');
    }

    public function show(Order $order)
    {
        abort_unless(
            $order->items()->where('seller_id', auth()->id())->exists(),
            404
        );

        $order->load([
            'customer:id,name,phone,email',
            'items' => fn ($query) => $query->where('seller_id', auth()->id())->orderBy('id'),
        ]);

        return view('seller.orders.show', compact('order'));
    }

    public function decide(Request $request, int $orderItem, StockService $stock)
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in([OrderItem::STATUS_ACCEPTED, OrderItem::STATUS_REJECTED])],
            'reason' => ['nullable', 'required_if:decision,rejected', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($data, $orderItem, $stock): Order {
            $item = OrderItem::query()
                ->where('seller_id', auth()->id())
                ->whereKey($orderItem)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $item->canBeDecided()) {
                throw ValidationException::withMessages([
                    'decision' => 'This item has already been decided.',
                ]);
            }

            $order = Order::query()->whereKey($item->order_id)->lockForUpdate()->firstOrFail();
            $decision = $data['decision'];
            $now = now();

            if ($decision === OrderItem::STATUS_REJECTED) {
                $product = $item->product_id ? Product::query()->find($item->product_id) : null;
                $variant = $item->product_variant_id
                    ? ProductVariant::query()->find($item->product_variant_id)
                    : null;

                if ($product && (! $product->has_variants || $variant)) {
                    $stock->adjust(
                        $product,
                        $variant,
                        $item->quantity,
                        'return',
                        'Rejected item from order ' . $order->order_number,
                        auth()->id(),
                        $item
                    );
                }

                $item->update([
                    'status' => OrderItem::STATUS_REJECTED,
                    'status_reason' => $data['reason'],
                    'rejected_at' => $now,
                    'stock_released_at' => $now,
                ]);

                $order->payable_total = max(0, round((float) $order->payable_total - (float) $item->line_total, 2));
            } else {
                $item->update([
                    'status' => OrderItem::STATUS_ACCEPTED,
                    'status_reason' => null,
                    'accepted_at' => $now,
                ]);
            }

            $order->tracking()->create([
                'order_item_id' => $item->id,
                'seller_id' => auth()->id(),
                'status' => $decision,
                'title' => $decision === OrderItem::STATUS_ACCEPTED
                    ? 'Seller accepted an item'
                    : 'Seller rejected an item (' . $item->product_name . ')',
                'description' => $decision === OrderItem::STATUS_REJECTED
                    ? $data['reason']
                    : $item->product_name . ' is being prepared by the seller.',
                'is_public' => true,
                'created_by' => auth()->id(),
            ]);

            $statuses = $order->items()->pluck('status');
            $activeStatuses = $statuses->reject(fn ($status) => in_array($status, [
                OrderItem::STATUS_REJECTED,
                OrderItem::STATUS_CANCELLED,
            ], true));

            if ($activeStatuses->isEmpty()) {
                $orderStatus = Order::STATUS_CANCELLED;
                $order->payable_total = 0;
            } elseif ($activeStatuses->every(fn ($status) => $status === OrderItem::STATUS_DELIVERED)) {
                $orderStatus = Order::STATUS_COMPLETED;
            } elseif ($activeStatuses->contains(fn ($status) => in_array($status, [
                OrderItem::STATUS_ACCEPTED,
                OrderItem::STATUS_SHIPPED,
                OrderItem::STATUS_DELIVERED,
            ], true))) {
                $orderStatus = Order::STATUS_PROCESSING;
            } else {
                $orderStatus = Order::STATUS_PENDING;
            }

            $order->update([
                'status' => $orderStatus,
                'payable_total' => $order->payable_total,
                'completed_at' => $orderStatus === Order::STATUS_COMPLETED ? ($order->completed_at ?? $now) : null,
                'cancelled_at' => $orderStatus === Order::STATUS_CANCELLED ? ($order->cancelled_at ?? $now) : null,
            ]);

            $order->payments()
                ->where('status', OrderPayment::STATUS_PENDING)
                ->update(['amount' => $order->payable_total]);

            return $order;
        });

        return redirect()
            ->route('seller.orders.show', $order)
            ->with('success', $data['decision'] === OrderItem::STATUS_ACCEPTED
                ? 'Item accepted.'
                : 'Item rejected and available stock restored.');
    }
}
