<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\MarketplaceNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = Shop::query()
                ->with('seller:id,name')
                ->select(['id', 'seller_id', 'name', 'banner', 'slug', 'address', 'status', 'is_feature_shop']);

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['pending', 'active', 'inactive'], true)) {
                        $query->where('status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('banner', function (Shop $shop) {
                    $imageUrl = $shop->banner
                        ? asset('storage/'.$shop->banner)
                        : asset('assets/images/no_image.png');

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.e($shop->name)
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->addColumn('seller_name', fn (Shop $shop) => $shop->seller?->name ?: 'N/A')
                ->editColumn('status', function (Shop $shop) {
                    $statusClass = match ($shop->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($shop->status)).'</span>';
                })
                ->editColumn('is_feature_shop', fn (Shop $shop) => $shop->is_feature_shop ? 'Yes' : 'No')
                ->addColumn('actions', function (Shop $shop) {
                    $showUrl = route('admin.shops.show', $shop);
                    $editUrl = route('admin.shops.edit', $shop);
                    $name = e($shop->name);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View shop" aria-label="View '.$name
                        .'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit shop status" aria-label="Edit status for '.$name
                        .'"><i class="bi bi-pencil"></i></a></div>';
                })
                ->removeColumn('id')
                ->rawColumns(['banner', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.shops.index');
    }

    public function show(Shop $shop)
    {
        $shop->load('seller:id,name,email');

        return view('admin.shops.show', compact('shop'));
    }

    public function edit(Shop $shop)
    {
        $shop->load('seller:id,name,email');

        return view('admin.shops.edit', compact('shop'));
    }

    public function update(
        Request $request,
        Shop $shop,
        MarketplaceNotificationService $notifications,
    )
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],
            'is_feature_shop' => ['required', 'boolean'],
        ]);

        $previousStatus = $shop->status;
        $shop->update($validated);

        if ($previousStatus !== $shop->status && $shop->status !== 'pending') {
            $notifications->notifySellerOfStatus(
                $shop->seller,
                'shop',
                $shop->name,
                $shop->status,
                route('seller.shops.show', $shop, false),
                'bi bi-shop',
            );
        }

        return redirect()->route('admin.shops.index')->with('success', 'Shop settings updated successfully.');
    }
}
