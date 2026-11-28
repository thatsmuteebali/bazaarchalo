<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderPlacementService;
use App\Exceptions\CartException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function account(){
        return view('customer.account');
    }
    public function profile(){
        return view('customer.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($request->user()->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['nullable', 'regex:/^03\d{9}$/', Rule::unique('users')->ignore($request->user()->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], [
            'phone.regex' => 'Enter a Pakistani mobile number in the format 03000000000.',
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user = $request->user();
        $oldImage = $user->image;
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profiles', 'public');
        }

        $user->update($validated);

        if ($request->hasFile('image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('customer.profile')->with('status', 'Your profile has been updated.');
    }

    public function orders(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->withSum('items', 'quantity')
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    public function orderDetail(Request $request)
    {
        $order = $this->customerOrder($request, false);
        if (! $order) {
            return redirect()->route('customer.orders')->with('status', 'That order could not be found in your account.');
        }

        return view('customer.order-detail', compact('order'));
    }

    public function storeOrderItemReview(Request $request, Order $order, OrderItem $orderItem)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);
        abort_unless((int) $orderItem->order_id === (int) $order->id, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($orderItem, $request, $data): void {
            $item = OrderItem::query()->whereKey($orderItem->id)->lockForUpdate()->firstOrFail();

            if ($item->status !== OrderItem::STATUS_DELIVERED) {
                throw ValidationException::withMessages([
                    'review' => 'You can review this product after it has been delivered.',
                ]);
            }

            if ($item->review()->exists()) {
                throw ValidationException::withMessages([
                    'review' => 'You have already reviewed this product.',
                ]);
            }

            $item->review()->create([
                'product_id' => $item->product_id,
                'customer_id' => $request->user()->id,
                'rating' => $data['rating'],
                'description' => $data['description'],
            ]);
        });

        return redirect()
            ->route('customer.order-detail', ['order' => $order->order_number])
            ->with('success', 'Thank you for sharing your product review.');
    }
    public function orderTracking(Request $request)
    {
        $order = $this->customerOrder($request, true);
        $tracking = $order
            ? $order->tracking()->reorder()->where('is_public', true)->orderBy('created_at')->orderBy('id')->get()
            : collect();

        return view('customer.order-tracking', [
            'order' => $order,
            'tracking' => $tracking,
            'orderNotFound' => $request->filled('order') && ! $order,
        ]);
    }

    public function checkout(CartService $cart, CheckoutService $checkout)
    {
        $summary = $cart->summary();

        // nothing to pay for: send the customer back to the cart
        if (empty($summary['items'])) {
            return redirect()->route('frontend.cart');
        }

        return view('customer.checkout', [
            'cart'             => $summary,
            'totals'           => $checkout->totals($summary),
            'googleMapsApiKey' => config('services.google_maps.key'),
            'user'              => auth()->user(),
            'paymentMethods'    => PaymentMethod::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function placeOrder(CheckoutRequest $request, CartService $cart, OrderPlacementService $orders)
    {
        try {
            $order = $orders->place($request->user(), $cart->summary(), $request->validated());
        } catch (CartException $exception) {
            return back()->withInput()->withErrors(['cart' => $exception->getMessage()]);
        }

        $cart->clear();

        return redirect()->route('customer.order-success', ['order' => $order->order_number]);
    }

    public function orderSuccess(Request $request)
    {
        $order = $this->customerOrder($request, false);
        if (! $order) {
            return redirect()->route('customer.orders')->with('status', 'No order confirmation was found.');
        }

        return view('customer.order-success', compact('order'));
    }
    public function wishlist(Request $request)
    {
        $userId = (int) $request->user()->id;
        $wishlistItems = $request->user()->wishlists()
            ->whereHas('product', fn ($query) => $query
                ->where('status', 'active')
                ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
                ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active')))
            ->with(['product' => fn ($query) => $query
                ->with(['category', 'coverImage', 'images', 'variants'])
                ->withCount(['variants', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->withMin('variants', 'price')
                ->withMax('variants', 'price')
                ->withWishlistStatus($userId)])
            ->latest()
            ->paginate(12);

        return view('customer.wishlist', compact('wishlistItems'));
    }

    public function toggleWishlist(Request $request, Product $product)
    {
        abort_unless(
            $product->status === 'active'
                && $product->shop()->where('status', 'active')->exists()
                && $product->category()->where('status', 'active')->exists(),
            404,
        );

        $wishlist = $request->user()->wishlists()
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Product removed from your wishlist.';
            $wishlisted = false;
        } else {
            $request->user()->wishlists()->create(['product_id' => $product->id]);
            $message = 'Product added to your wishlist.';
            $wishlisted = true;
        }

        if ($request->expectsJson()) {
            $wishlistCount = $request->user()->wishlists()->count();
            $visibleWishlistCount = $request->user()->wishlists()
                ->whereHas('product', fn ($query) => $query
                    ->where('status', 'active')
                    ->whereHas('shop', fn ($shopQuery) => $shopQuery->where('status', 'active'))
                    ->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('status', 'active')))
                ->count();

            return response()->json([
                'ok' => true,
                'wishlisted' => $wishlisted,
                'wishlist_count' => $wishlistCount,
                'visible_wishlist_count' => $visibleWishlistCount,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function addresses(){
        return view('customer.addresses');
    }

    private function customerOrder(Request $request, bool $latestWhenMissing): ?Order
    {
        $query = Order::query()->where('user_id', $request->user()->id);
        if ($request->filled('order')) {
            return $query->with(['items.review', 'payments'])
                ->where('order_number', $request->query('order'))
                ->first();
        }

        return $latestWhenMissing
            ? $query->with(['items.review', 'payments'])->latest()->first()
            : null;
    }
}
