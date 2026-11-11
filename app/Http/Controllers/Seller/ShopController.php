<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\MarketplaceNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = auth()->user()->shops()
                ->select(['id', 'name', 'banner', 'address', 'status', 'deleted_at']);

            if ($status === 'trashed') {
                $query->onlyTrashed();
            }

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
                    $name = e($shop->name);

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.$name
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->editColumn('status', function (Shop $shop) {
                    $statusLabel = match ($shop->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusLabel.'">'
                        .e(ucfirst($shop->status)).'</span>';
                })
                ->addColumn('actions', function (Shop $shop) {
                    $name = e($shop->name);

                    if ($shop->trashed()) {
                        $restoreUrl = route('seller.shops.restore', $shop);
                        $forceUrl = route('seller.shops.force-delete', $shop);

                        return '<div class="d-flex justify-content-center gap-1">'
                            .'<form method="POST" action="'.e($restoreUrl).'">'
                            .csrf_field().method_field('PATCH')
                            .'<button type="submit" class="table-btn-action restore" title="Restore shop" aria-label="Restore '
                            .$name.'"><i class="bi bi-arrow-counterclockwise"></i></button></form>'
                            .'<form method="POST" action="'.e($forceUrl).'">'
                            .csrf_field().method_field('DELETE')
                            .'<button type="button" class="table-btn-action delete" title="Delete permanently" aria-label="Delete '
                            .$name.' permanently" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                            .' data-item-name="'.$name.'" data-item-type="shop">'
                            .'<i class="bi bi-trash"></i></button></form></div>';
                    }

                    $showUrl = route('seller.shops.show', $shop);
                    $editUrl = route('seller.shops.edit', $shop);
                    $deleteUrl = route('seller.shops.destroy', $shop);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View shop" aria-label="View '
                        .$name.'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit shop" aria-label="Edit '
                        .$name.'"><i class="bi bi-pencil"></i></a>'
                        .'<form method="POST" action="'.e($deleteUrl).'">'
                        .csrf_field().method_field('DELETE')
                        .'<button type="button" class="table-btn-action delete" title="Delete shop" aria-label="Delete '
                        .$name.'" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-delete'
                        .' data-item-name="'.$name.'" data-item-type="shop">'
                        .'<i class="bi bi-trash"></i></button></form></div>';
                })
                ->removeColumn('id', 'deleted_at')
                ->rawColumns(['banner', 'status', 'actions'])
                ->toJson();
        }

        return view('seller.shops.index');
    }

    public function create()
    {
        return view('seller.shops.create');
    }

    public function store(Request $request, MarketplaceNotificationService $notifications)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'banner' => ['required', 'image', 'max:4096'],
            'address' => ['required', 'string', 'max:5000'],
            'is_primary' => ['nullable', 'boolean'],
            'slug' => ['required', 'string', 'unique:shops,slug','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ],[
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and single hyphens (e.g. apple-iphone-15).',
        ]);

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('shops', 'public');
        }

        $shop = auth()->user()->shops()->create($validated + ['status' => 'pending']);
        $notifications->notifyAdminsOfPendingSubmission(
            'shop',
            $shop->name,
            auth()->user(),
            route('admin.shops.show', $shop, false),
            'bi bi-shop',
        );

        return redirect()->route('seller.shops.index')->with('success', 'Shop created successfully.');
    }

    public function show(Shop $shop)
    {
        $this->ensureOwnership($shop);

        return view('seller.shops.show', compact('shop'));
    }

    public function edit(Shop $shop)
    {
        $this->ensureOwnership($shop);

        return view('seller.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $this->ensureOwnership($shop);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'address' => ['nullable', 'string', 'max:5000'],
            'is_primary' => ['nullable', 'boolean'],
            'slug' => ['required', 'string', Rule::unique('shops', 'slug')->ignore($shop->id),'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ],[
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and single hyphens (e.g. apple-iphone-15).',
        ]);

        if ($request->hasFile('banner')) {
            if ($shop->banner) {
                Storage::disk('public')->delete($shop->banner);
            }
            $validated['banner'] = $request->file('banner')->store('shops', 'public');
        }

        $shop->update($validated);

        return redirect()->route('seller.shops.index')->with('success', 'Shop updated successfully.');
    }

    public function destroy(Request $request, Shop $shop)
    {
        $this->ensureOwnership($shop);
        $shop->delete();

        return redirect()
            ->route('seller.shops.index', $request->only('search', 'status', 'page'))
            ->with('success', 'Shop deleted successfully.');
    }

    public function restore(Shop $shop)
    {
        $this->ensureOwnership($shop);
        $shop->restore();

        return redirect()
            ->route('seller.shops.index')
            ->with('success', 'Shop restored successfully.');
    }

    public function forceDelete(Shop $shop)
    {
        $this->ensureOwnership($shop);
        $banner = $shop->banner;
        $shop->forceDelete();

        if ($banner) {
            Storage::disk('public')->delete($banner);
        }

        return redirect()
            ->route('seller.shops.index')
            ->with('success', 'Shop permanently deleted.');
    }

    private function ensureOwnership(Shop $shop): void
    {
        abort_unless((int) $shop->seller_id === (int) auth()->id(), 404);
    }
}
