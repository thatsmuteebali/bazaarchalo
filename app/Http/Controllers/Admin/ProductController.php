<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\MarketplaceNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $query = Product::query()
                ->with(['seller:id,name', 'shop:id,name', 'collection:id,name', 'category:id,name', 'coverImage'])
                ->withCount('variants')
                ->withMin('variants', 'price')
                ->select([
                    'products.id',
                    'products.seller_id',
                    'products.shop_id',
                    'products.collection_id',
                    'products.category_id',
                    'products.name',
                    'products.slug',
                    'products.price',
                    'products.stock',
                    'products.has_variants',
                    'products.status',
                    'products.is_featured',
                ]);

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($status) {
                    if (in_array($status, ['pending', 'active', 'inactive'], true)) {
                        $query->where('products.status', $status);
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('image', function (Product $product) {
                    $imageUrl = $product->coverImage?->url ?: asset('assets/images/no_image.png');

                    return '<div class="table-user-cell"><img src="'.e($imageUrl).'" alt="'.e($product->name)
                        .'" class="table-user-avatar" onerror="this.src=\''
                        .e(asset('assets/images/no_image.png')).'\'"></div>';
                })
                ->addColumn('seller_name', fn (Product $product) => $product->seller?->name ?: 'N/A')
                ->addColumn('shop_name', fn (Product $product) => $product->shop?->name ?: 'N/A')
                ->addColumn('collection_name', fn (Product $product) => $product->collection?->name ?: 'N/A')
                ->addColumn('category_name', fn (Product $product) => $product->category?->name ?: 'N/A')
                ->editColumn('name', function (Product $product) {
                    $name = e($product->name);
                    $featured = $product->is_featured
                        ? ' <i class="bi bi-star-fill text-warning" title="Featured"></i>'
                        : '';
                    $variants = $product->has_variants
                        ? '<div class="table-user-sub">'.$product->variants_count.' variants</div>'
                        : '';

                    return '<div class="table-user-name">'.$name.$featured.$variants.'</div>';
                })
                ->editColumn('price', function (Product $product) {
                    $price = $product->has_variants ? $product->variants()->min('price') : $product->price;

                    return $price === null ? 'N/A' : number_format((float) $price, 2);
                })
                ->orderColumn('price', 'CASE WHEN products.has_variants = 1 THEN (SELECT MIN(product_variants.price) FROM product_variants WHERE product_variants.product_id = products.id) ELSE products.price END $1')
                ->editColumn('status', function (Product $product) {
                    $statusClass = match ($product->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };

                    return '<span class="badge-table '.$statusClass.'">'.e(ucfirst($product->status)).'</span>';
                })
                ->addColumn('actions', function (Product $product) {
                    $showUrl = route('admin.products.show', $product);
                    $editUrl = route('admin.products.edit', $product);
                    $name = e($product->name);

                    return '<div class="d-flex justify-content-center gap-1">'
                        .'<a href="'.e($showUrl).'" class="table-btn-action" title="View product" aria-label="View '
                        .$name.'"><i class="bi bi-eye"></i></a>'
                        .'<a href="'.e($editUrl).'" class="table-btn-action" title="Edit product settings" aria-label="Edit settings for '
                        .$name.'"><i class="bi bi-pencil"></i></a></div>';
                })
                ->removeColumn('id', 'is_featured')
                ->rawColumns(['image', 'name', 'status', 'actions'])
                ->toJson();
        }

        return view('admin.products.index');
    }

    public function show(Product $product)
    {
        $product->load([
            'seller:id,name,email',
            'shop:id,name',
            'category:id,name',
            'collection:id,name',
            'images' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'variants' => fn ($query) => $query->orderBy('id'),
        ]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('seller:id,name,email', 'shop:id,name', 'category:id,name');

        return view('admin.products.edit', compact('product'));
    }

    public function update(
        Request $request,
        Product $product,
        MarketplaceNotificationService $notifications,
    )
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'inactive'])],
            'is_featured' => ['required', 'boolean'],
        ]);

        $previousStatus = $product->status;
        $product->update($validated);

        if ($previousStatus !== $product->status && $product->status !== 'pending') {
            $notifications->notifySellerOfStatus(
                $product->seller,
                'product',
                $product->name,
                $product->status,
                route('seller.products.show', $product, false),
                'bi bi-box-seam',
            );
        }

        return redirect()->route('admin.products.index')->with('success', 'Product settings updated successfully.');
    }
}
