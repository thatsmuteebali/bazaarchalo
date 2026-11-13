<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\HeroSectionSliderImage;
use App\Models\Product;
use App\Models\Shop;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function index()
    {
        $home_page_content = getWebsiteContent('home');
        $hero_slider = HeroSectionSliderImage::all();

        $categories = Category::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            // ->whereHas('products', fn ($query) => $query->where('status', 'active')
            //     ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active')))
            // ->latest()
            // ->limit(6)
            ->get();
        $shops = Shop::query()
            ->where('status', 'active')
            ->where('is_feature_shop', true)
            // ->whereHas('products', fn ($query) => $query->where('status', 'active')
            //     ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active')))
            // ->latest()
            // ->limit(3)
            ->get();
        $featuredCollections = Collection::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            // ->whereHas('products', fn ($query) => $query->where('status', 'active')
            //     ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
            //     ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active')))
            // ->latest()
            // ->limit(8)
            ->get();
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->whereHas('category', fn ($query) => $query->where('status', 'active'))
            ->with(['shop:id,name,slug', 'category:id,name,slug', 'coverImage', 'images', 'variants'])
            ->withMin('variants', 'price')->withMax('variants', 'price')->withCount('variants')
            ->latest()
            ->limit(8)
            ->get();

        return view('frontend.home', [
            'home_page_content' => $home_page_content,
            'hero_slider' => $hero_slider,
            'categories' => $categories,
            'shops' => $shops,
            'featuredCollections' => $featuredCollections,
            'featuredProducts' => $featuredProducts,
        ]);
    }
    public function shop(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlugs = collect((array) $request->query('category', []))
            ->filter(fn ($slug) => is_string($slug))
            ->map(fn ($slug) => trim($slug))
            ->filter(fn ($slug) => $slug !== '')
            ->unique()
            ->values()
            ->all();
        $shopSlugs = collect((array) $request->query('shop', []))
            ->filter(fn ($slug) => is_string($slug))
            ->map(fn ($slug) => trim($slug))
            ->filter(fn ($slug) => $slug !== '')
            ->unique()
            ->values()
            ->all();
        $collectionSlugs = collect((array) $request->query('collection', []))
            ->filter(fn ($slug) => is_string($slug))
            ->map(fn ($slug) => trim($slug))
            ->filter(fn ($slug) => $slug !== '')
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
            ->when($categorySlugs !== [], fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->whereIn('slug', $categorySlugs)))
            ->when($shopSlugs !== [], fn ($query) => $query->whereHas('shop', fn ($shopQuery) => $shopQuery->whereIn('slug', $shopSlugs)))
            ->when($collectionSlugs !== [], fn ($query) => $query->whereHas('collection', fn ($collectionQuery) => $collectionQuery
                ->whereIn('slug', $collectionSlugs)
                ->where('status', 'active')))
            ->when($minPrice > 0 || $maxPrice < $priceLimit, fn ($query) => $query->where(function ($query) use ($minPrice, $maxPrice) {
                $query->where(function ($query) use ($minPrice, $maxPrice) {
                    $query->where('has_variants', false)->whereBetween('price', [$minPrice, $maxPrice]);
                })->orWhere(function ($query) use ($minPrice, $maxPrice) {
                    $query->where('has_variants', true)->whereHas('variants', fn ($query) => $query->whereBetween('price', [$minPrice, $maxPrice]));
                });
            }))
            ->with(['shop:id,name,slug', 'category:id,name,slug', 'coverImage', 'images', 'variants'])
            ->withCount('variants')
            ->withMin('variants', 'price')->withMax('variants', 'price')
            ->when($sort === 'price_asc', fn ($query) => $query->orderByRaw('CASE WHEN products.has_variants = 1 THEN (SELECT MIN(product_variants.price) FROM product_variants WHERE product_variants.product_id = products.id) ELSE products.price END ASC'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByRaw('CASE WHEN products.has_variants = 1 THEN (SELECT MIN(product_variants.price) FROM product_variants WHERE product_variants.product_id = products.id) ELSE products.price END DESC'))
            ->when($sort === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($sort, ['price_asc', 'price_desc', 'name'], true), fn ($query) => $query->latest());

        $products = $productsQuery->paginate(12)->withQueryString();
        $categories = Category::query()
            ->where('status', 'active')
            // ->whereHas('products', fn ($query) => $query->where('status', 'active')
            //     ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active')))
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))])
            ->orderBy('name')
            ->get();
        $shops = Shop::where('status', 'active')
                ->withCount(['products' => fn ($query) => $query->where('status', 'active')
                    ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
                    ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active'))])
                ->orderBy('name')->get(['id', 'name', 'slug']);
        $collections = Collection::query()
            ->where('status', 'active')
            // ->whereHas('products', fn ($query) => $query->where('status', 'active')
            //     ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
            //     ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active')))
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
                ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active'))])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
            ->whereHas('category', fn ($query) => $query->where('status', 'active'))
            ->with(['coverImage', 'variants'])
            ->withMin('variants', 'price')
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.shop', [
            'products' => $products,
            'categories' => $categories,
            'shops' => $shops,
            'collections' => $collections,
            'featuredProducts' => $featuredProducts,
            'filters' => compact('search', 'categorySlugs', 'shopSlugs', 'collectionSlugs', 'sort', 'minPrice', 'maxPrice', 'priceLimit'),
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
            'shop:id,name,slug',
            'category:id,name,slug',
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
            ->get(['slug', 'name']);

        $shops = Shop::query()
            ->where('status', 'active')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($category !== '', fn ($query) => $query->whereHas('products', fn ($query) => $query
                ->where('status', 'active')
                ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category)->where('status', 'active'))))
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
    public function cart(CartService $cart)
   {
       return view('frontend.cart', ['cart' => $cart->summary()]);
   }
}
