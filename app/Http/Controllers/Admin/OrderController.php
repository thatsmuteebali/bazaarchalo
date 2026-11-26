<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\OrderDispatchService;
use App\Services\OrderItemDecisionService;
use App\Services\OrderTrackingService;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $query = Order::query()
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
                ->editColumn('created_at', function (Order $order) {
                    return $order->created_at->format('M j, Y')
                        .'<small class="d-block text-muted">'.$order->created_at->format('g:i A').'</small>';
                })
                ->addColumn('items', function (Order $order) {
                    $quantity = (int) ($order->items_sum_quantity ?? 0);

                    return $quantity.' '.($quantity === 1 ? 'unit' : 'units');
                })
                ->editColumn('payable_total', fn (Order $order) => Money::format($order->payable_total))
                ->editColumn('status', function (Order $order) {
                    $statusClass = match ($order->status) {
                        Order::STATUS_PROCESSING, Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_CANCELLED => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($order->status)).'</span>';
                })
                ->editColumn('payment_status', function (Order $order) {
                    $statusClass = match ($order->payment_status) {
                        Order::PAYMENT_PAID => 'success',
                        Order::PAYMENT_FAILED, Order::PAYMENT_REFUNDED => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($order->payment_status)).'</span>';
                })
                ->addColumn('actions', function (Order $order) {
                    $url = e(route('admin.orders.show', $order));
                    $orderNumber = e($order->order_number);

                    return '<div class="d-flex justify-content-center">'
                        .'<a href="'.$url.'" class="table-btn-action" title="View order" aria-label="View order '
                        .$orderNumber.'"><i class="bi bi-eye" aria-hidden="true"></i></a></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['order', 'customer', 'created_at', 'status', 'payment_status', 'actions'])
                ->toJson();
        }

        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load([
            'customer:id,name,email,phone',
            'rider:id,name,phone',
            'items' => fn ($query) => $query->with(['seller:id,name', 'shop:id,name'])->orderBy('id'),
            'tracking' => fn ($query) => $query
                ->with(['item:id,product_name', 'creator:id,name'])
                ->reorder()
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        $riders = User::query()
            ->where('role', 'rider')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);
        $allItemsDecided = $order->items->isNotEmpty()
            && $order->items->every(fn (OrderItem $item) => ! $item->canBeDecided());
        $hasAcceptedItems = $order->items->contains(
            fn (OrderItem $item) => $item->status === OrderItem::STATUS_ACCEPTED,
        );

        return view('admin.orders.show', compact('order', 'riders', 'allItemsDecided', 'hasAcceptedItems'));
    }

    public function assignRider(Request $request, Order $order, OrderDispatchService $dispatch)
    {
        $data = $request->validate([
            'rider_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'rider')->where('status', 'Active'),
            ],
        ]);

        $dispatch->assignRider($order, (int) $data['rider_id'], $request->user());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'The rider has been assigned and order notifications have been sent.');
    }

    public function decide(
        Request $request,
        Order $order,
        OrderItem $orderItem,
        OrderItemDecisionService $decisions,
    ) {
        abort_unless((int) $orderItem->order_id === (int) $order->id, 404);

        $data = $request->validate([
            'decision' => ['required', Rule::in([OrderItem::STATUS_ACCEPTED, OrderItem::STATUS_REJECTED])],
            'reason' => ['nullable', 'required_if:decision,rejected', 'string', 'max:255'],
        ]);

        $decisions->decide(
            $orderItem->id,
            $data['decision'],
            $data['reason'] ?? null,
            $request->user(),
            'Admin',
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $data['decision'] === OrderItem::STATUS_ACCEPTED
                ? 'Order item accepted.'
                : 'Order item rejected and available stock restored.');
    }

    public function storeTracking(Request $request, Order $order, OrderTrackingService $tracking)
    {
        $data = $request->validate([
            'order_item_id' => [
                'nullable',
                'required_if:status,shipped,delivered',
                'integer',
                Rule::exists('order_items', 'id')->where('order_id', $order->id),
            ],
            'status' => ['required', Rule::in(['note'])],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'courier_name' => ['nullable', 'string', 'max:80'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'is_public' => ['sometimes', 'boolean'],
        ]);
        $data['is_public'] = $request->boolean('is_public');

        $tracking->addUpdate($order, $data, $request->user());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Tracking update added.');
    }
}
