<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderItemDecisionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function decide(Request $request, int $orderItem, OrderItemDecisionService $decisions)
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in([OrderItem::STATUS_ACCEPTED, OrderItem::STATUS_REJECTED])],
            'reason' => ['nullable', 'required_if:decision,rejected', 'string', 'max:255'],
        ]);

        $seller = auth()->user();
        $order = $decisions->decide(
            $orderItem,
            $data['decision'],
            $data['reason'] ?? null,
            $seller,
            'Seller',
            $seller->id,
        );

        return redirect()
            ->route('seller.orders.show', $order)
            ->with('success', $data['decision'] === OrderItem::STATUS_ACCEPTED
                ? 'Item accepted.'
                : 'Item rejected and available stock restored.');
    }
}
