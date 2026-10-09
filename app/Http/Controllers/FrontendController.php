<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')->latest()->limit(6)->get();
        $shops = Shop::where('status', 'active')->latest()->limit(3)->get();
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->with(['shop:id,name', 'category:id,name', 'coverImage', 'images', 'variants'])
            ->withMin('variants', 'price')->withMax('variants', 'price')->withCount('variants')
            ->latest()
            ->limit(8)
            ->get();

        return view('frontend.home', [
            'categories' => $categories,
            'shops' => $shops,
            'featuredProducts' => $featuredProducts,
        ]);
    }
    public function shop(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $categoryIds = collect((array) $request->query('category', []))
            ->filter(fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
        $shopIds = collect((array) $request->query('shop', []))
            ->filter(fn ($id) => filter_var($id, FILTER_VALIDATE_INT) !== false)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
        $sort = (string) $request->query('sort', 'latest');

        $maxSimplePrice = (float) (Product::query()
            ->where('status', 'active')
            ->where('has_variants', false)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->max('price') ?? 0);
        $maxVariantPrice = (float) (DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('shops', 'shops.id', '=', 'products.shop_id')
            ->where('products.status', 'active')
            ->where('shops.status', 'active')
            ->max('product_variants.price') ?? 0);
        $priceLimit = max(1, (int) ceil(max($maxSimplePrice, $maxVariantPrice)));
        $minPrice = is_numeric($request->query('min_price'))
            ? min($priceLimit, max(0, (float) $request->query('min_price')))
            : 0;
        $maxPrice = is_numeric($request->query('max_price'))
            ? min($priceLimit, max($minPrice, (float) $request->query('max_price')))
            : $priceLimit;

        $productsQuery = Product::query()
            ->where('status', 'active')
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->whereHas('category', fn ($query) => $query->where('status', 'active'))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            }))
            ->when($categoryIds !== [], fn ($query) => $query->whereIn('category_id', $categoryIds))
            ->when($shopIds !== [], fn ($query) => $query->whereIn('shop_id', $shopIds))
            ->when($minPrice > 0 || $maxPrice < $priceLimit, fn ($query) => $query->where(function ($query) use ($minPrice, $maxPrice) {
                $query->where(function ($query) use ($minPrice, $maxPrice) {
                    $query->where('has_variants', false)->whereBetween('price', [$minPrice, $maxPrice]);
                })->orWhere(function ($query) use ($minPrice, $maxPrice) {
                    $query->where('has_variants', true)->whereHas('variants', fn ($query) => $query->whereBetween('price', [$minPrice, $maxPrice]));
                });
            }))
            ->with(['shop:id,name', 'category:id,name', 'coverImage', 'images', 'variants'])
            ->withCount('variants')
            ->withMin('variants', 'price')->withMax('variants', 'price')
            ->when($sort === 'price_asc', fn ($query) => $query->orderByRaw('CASE WHEN products.has_variants = 1 THEN (SELECT MIN(product_variants.price) FROM product_variants WHERE product_variants.product_id = products.id) ELSE products.price END ASC'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByRaw('CASE WHEN products.has_variants = 1 THEN (SELECT MIN(product_variants.price) FROM product_variants WHERE product_variants.product_id = products.id) ELSE products.price END DESC'))
            ->when($sort === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($sort, ['price_asc', 'price_desc', 'name'], true), fn ($query) => $query->latest());

        $products = $productsQuery->paginate(12)->withQueryString();
        $categories = Category::query()
            ->where('status', 'active')
            ->whereHas('products', fn ($query) => $query->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active')))
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))])
            ->orderBy('name')
            ->get();
        $shops = Shop::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->with(['coverImage', 'variants'])
            ->withMin('variants', 'price')
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.shop', [
            'products' => $products,
            'categories' => $categories,
            'shops' => $shops,
            'featuredProducts' => $featuredProducts,
            'filters' => compact('search', 'categoryIds', 'shopIds', 'sort', 'minPrice', 'maxPrice', 'priceLimit'),
        ]);
    }
    public function about()
    {
        return view('frontend.about');
    }
    public function contact()
    {
        return view('frontend.contact');
    }
    public function productDetail(?Product $product = null)
    {
        if (! $product) {
            return redirect()->route('frontend.shop');
        }

        abort_unless(
            $product->status === 'active'
                && $product->shop()->where('status', 'active')->exists()
                && $product->category()->where('status', 'active')->exists(),
            404,
        );

        $product->load([
            'shop:id,name',
            'category:id,name',
            'images' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'options' => fn ($query) => $query->orderBy('position'),
            'variants' => fn ($query) => $query->orderBy('id'),
        ]);

        $relatedProducts = Product::query()
            ->where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->with(['category:id,name', 'coverImage', 'images', 'variants'])
            ->withMin('variants', 'price')->withMax('variants', 'price')->withCount('variants')
            ->latest()
            ->limit(4)
            ->get();

        return view('frontend.product-show', compact('product', 'relatedProducts'));
    }
    public function categories()
    {
        $categories = Category::where('status', 'active')
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')])
            ->latest()
            ->get();
        return view('frontend.categories', [
            'categories' => $categories,
        ]);
    }
    public function deliveryPolicy()
    {
        return view('frontend.delivery-policy');
    }
    public function faq()
    {
        return view('frontend.faq');
    }
    public function offers()
    {
        return view('frontend.offers');
    }
    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }
    public function refundPolicy()
    {
        return view('frontend.refund-policy');
    }
    public function shopListing(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $categories = Category::query()
            ->where('status', 'active')
            ->whereHas('products', fn ($query) => $query->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active')))
            ->orderBy('name')
            ->get(['id', 'name']);

        $shops = Shop::query()
            ->where('status', 'active')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($category !== '', fn ($query) => $query->whereHas('products', fn ($query) => $query
                ->where('status', 'active')
                ->where('category_id', $category)))
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.shop-listing', [
            'shops' => $shops,
            'categories' => $categories,
            'filters' => compact('search', 'category'),
        ]);
    }
    public function termsAndConditions()
    {
        return view('frontend.terms-and-conditions');
    }
    public function cart()
    {
        return view('frontend.cart');
    }
}
