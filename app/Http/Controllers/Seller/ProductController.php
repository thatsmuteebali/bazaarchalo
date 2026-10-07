<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductController extends Controller
{
    private const FIELDS = [
        'shop_id',
        'category_id',
        'collection_id',
        'name',
        'short_description',
        'description',
        'price',
        'compare_price',
        'status',
    ];

    /* ------------------------------------------------------------------ */
    /*  LIST                                                              */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $perPage = in_array((int) $request->query('per_page'), [10, 20, 50], true) ? (int) $request->query('per_page') : 10;

        $products = Product::query()
            ->where('seller_id', auth()->id())
            ->with(['shop:id,name', 'category:id,name', 'coverImage'])
            ->withCount('variants')
            ->withMin('variants', 'price') // -> $product->variants_min_price (no extra query per row)
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when(in_array($status, ['active', 'draft', 'inactive'], true), fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE                                                            */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        return view('seller.products.create', $this->formData());
    }

    public function store(ProductRequest $request, StockService $stock)
    {
        $data = $request->validated();
        $hasVariants = (bool) ($data['has_variants'] ?? false);
        $stored = []; // uploaded files, deleted again if something fails

        try {
            DB::transaction(function () use ($request, $data, $hasVariants, $stock, &$stored) {
                $product = Product::create($this->attributes($data) + [
                    'seller_id' => auth()->id(),
                    'is_featured' => (bool) ($data['is_featured'] ?? false),
                    'has_variants' => $hasVariants,
                    'stock' => 0, // stock is only ever changed through StockService
                ]);

                $this->storeImages($product, Arr::wrap($request->file('images')), 0, $stored);

                if ($hasVariants) {
                    $this->saveOptions($product, $data['options']);

                    foreach (array_values($data['variants']) as $v) {
                        $variant = $product->variants()->create($this->variantAttributes($v) + [
                            'title' => $v['title'],
                            'stock' => 0,
                        ]);

                        $qty = (int) ($v['stock'] ?? 0);
                        if ($qty > 0) {
                            $stock->adjust($product, $variant, $qty, 'initial', null, auth()->id());
                        }
                    }
                } else {
                    $qty = (int) ($data['stock'] ?? 0);
                    if ($qty > 0) {
                        $stock->adjust($product, null, $qty, 'initial', null, auth()->id());
                    }
                }
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($stored);
            throw $e;
        }

        return redirect()->route('seller.products.index')->with('success', 'Product created successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  VIEW                                                              */
    /* ------------------------------------------------------------------ */
    public function show(Product $product)
    {
        $this->ownProduct($product);

        $product->load([
            'shop:id,name',
            'category:id,name',
            'collection:id,name',
            'images' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'options' => fn($q) => $q->orderBy('position'),
            'variants' => fn($q) => $q->orderBy('id'),
        ]);

        $movements = $product->movements()
            ->with('user:id,name')
            ->latest('created_at')->latest('id')
            ->limit(5)
            ->get();

        return view('seller.products.show', compact('product', 'movements'));
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT                                                              */
    /* ------------------------------------------------------------------ */
    public function edit(Product $product)
    {
        $this->ownProduct($product);

        $product->load([
            'images' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'options' => fn($q) => $q->orderBy('position'),
            'variants' => fn($q) => $q->orderBy('id'),
        ]);

        $movements = $product->movements()
            ->with('user:id,name')
            ->latest('created_at')->latest('id')
            ->limit(15)
            ->get();

        return view('seller.products.edit', $this->formData() + compact('product', 'movements'));
    }

    public function update(ProductRequest $request, Product $product, StockService $stock)
    {
        $this->ownProduct($product);

        $data = $request->validated();
        $newPaths = [];
        $oldPaths = [];

        try {
            DB::transaction(function () use ($request, $data, $product, $stock, &$newPaths, &$oldPaths) {
                // has_variants and stock are never changed here
                $product->update($this->attributes($data) + [
                    'is_featured' => (bool) ($data['is_featured'] ?? false),
                ]);

                // ---- images: remove, add, re-number ----
                $removeIds = array_map('intval', Arr::wrap($data['remove_images'] ?? []));
                if ($removeIds) {
                    $product->images()->whereIn('id', $removeIds)->get()->each(function ($img) use (&$oldPaths) {
                        $oldPaths[] = $img->path;
                        $img->delete();
                    });
                }

                $max = ProductImage::where('product_id', $product->id)->max('sort_order');
                $this->storeImages($product, Arr::wrap($request->file('images')), $max === null ? 0 : $max + 1, $newPaths);

                $product->images()->orderBy('sort_order')->orderBy('id')->get()->each(function ($img, $i) {
                    if ((int) $img->sort_order !== $i) {
                        $img->update(['sort_order' => $i]);
                    }
                });

                // ---- options + variants ----
                if ($product->has_variants) {
                    $this->syncVariants($product, $data, $stock);
                }
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($newPaths);
            throw $e;
        }

        Storage::disk('public')->delete($oldPaths);

        return redirect()->route('seller.products.edit', $product)->with('success', 'Product updated successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  DELETE                                                            */
    /* ------------------------------------------------------------------ */
    public function destroy(Request $request, Product $product)
    {
        $this->ownProduct($product);

        $paths = $product->images()->pluck('path')->all();

        // images, options, variants and stock history are removed by the database (cascade)
        $product->delete();

        Storage::disk('public')->delete($paths);

        return redirect()->route('seller.products.index', $request->only(['q', 'status', 'page', 'per_page']))
            ->with('success', 'Product deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  HELPERS                                                           */
    /* ------------------------------------------------------------------ */
    private function ownProduct(Product $product): void
    {
        abort_unless((int) $product->seller_id === (int) auth()->id(), 403);
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'shops' => Shop::where('status', 'active')->where('seller_id', auth()->id())->orderBy('name')->get(),
            'collections' => Collection::where('status', 'active')->where('seller_id', auth()->id())->orderBy('name')->get(),
        ];
    }

    /** Product columns from validated data (missing nullable fields become null). */
    private function attributes(array $data): array
    {
        return Arr::only($data, self::FIELDS)
            + array_fill_keys(['collection_id', 'short_description', 'description', 'compare_price'], null);
    }

    private function variantAttributes(array $v): array
    {
        return [
            'option_values' => array_values($v['option_values']),
            'sku' => $v['sku'] ?? null,
            'price' => $v['price'],
            'compare_price' => $v['compare_price'] ?? null,
        ];
    }

    private function storeImages(Product $product, array $files, int $startOrder, array &$stored): void
    {
        foreach (array_values($files) as $i => $file) {
            $path = $file->store('products', 'public');
            $stored[] = $path;

            $product->images()->create(['path' => $path, 'sort_order' => $startOrder + $i]);
        }
    }

    private function saveOptions(Product $product, array $options): void
    {
        foreach (array_values($options) as $position => $option) {
            $product->options()->create([
                'name' => $option['name'],
                'values' => array_values($option['values']),
                'position' => $position,
            ]);
        }
    }

    /**
     * Variants are matched by title, so existing variants (and their stock history)
     * are updated in place - never deleted and recreated.
     */
    private function syncVariants(Product $product, array $data, StockService $stock): void
    {
        $product->options()->delete();
        $this->saveOptions($product, $data['options']);

        $existing = $product->variants()->get()->keyBy('title');
        $keep = [];

        foreach (array_values($data['variants']) as $v) {
            $keep[] = $v['title'];

            if ($variant = $existing->get($v['title'])) {
                $variant->update($this->variantAttributes($v)); // stock untouched
                continue;
            }

            // new combination: created with 0 stock, then starting stock goes through the service
            $variant = $product->variants()->create($this->variantAttributes($v) + [
                'title' => $v['title'],
                'stock' => 0,
            ]);

            $qty = (int) ($v['stock'] ?? 0);
            if ($qty > 0) {
                $stock->adjust($product, $variant, $qty, 'initial', null, auth()->id());
            }
        }

        // variants the seller removed
        foreach ($existing as $title => $variant) {
            if (in_array($title, $keep, true)) {
                continue;
            }

            $variant->refresh();
            if ($variant->stock > 0) {
                $stock->adjust($product, $variant, -$variant->stock, 'correction', 'Variant removed', auth()->id());
            }

            $variant->delete(); // history rows keep variant_title
        }
    }
}
