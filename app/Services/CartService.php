<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Models\ProductVariant;

/**
 * Shopping cart stored in the PHP session.
 *
 * The session only keeps  product id, variant id and quantity.
 * Name, image, price and stock are always read from the database, so a customer
 * can never change a price and old prices never stay in the cart.
 */
class CartService
{
    public const MAX_PER_LINE = 99;

    private const SESSION_KEY = 'cart_lines';

    /** Summary built during this request, so the layout, the page and the JSON do not rebuild it (and notices are not lost). */
    private ?array $summaryCache = null;

    /* ------------------------------------------------------------------ */
    /*  Changing the cart                                                 */
    /* ------------------------------------------------------------------ */

    public function add(int $productId, ?int $variantId = null, int $quantity = 1): array
    {
        $quantity = max(1, min(self::MAX_PER_LINE, $quantity));

        $product = $this->purchasable()->find($productId);
        if (! $product) {
            throw new CartException('Sorry, this product is no longer available.');
        }

        [$variant, $stock] = $this->stockFor($product, $variantId);

        if ($stock < 1) {
            throw new CartException('Sorry, this item is out of stock.');
        }

        $lines   = $this->lines();
        $key     = $this->lineKey($product->id, $variant?->id);
        $current = (int) ($lines[$key]['qty'] ?? 0);
        $wanted  = $current + $quantity;

        if ($wanted > $stock) {
            throw new CartException(
                $current > 0
                    ? "You already have {$current} in your cart and only {$stock} are available."
                    : "Only {$stock} available."
            );
        }

        $lines[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'qty'        => min($wanted, self::MAX_PER_LINE),
        ];

        $this->save($lines);

        return $this->summary();
    }

    public function update(string $key, int $quantity): array
    {
        $lines = $this->lines();

        if (! isset($lines[$key])) {
            throw new CartException('That item is not in your cart any more.');
        }

        if ($quantity < 1) {
            unset($lines[$key]);
            $this->save($lines);

            return $this->summary();
        }

        $product = $this->purchasable()->find($lines[$key]['product_id']);
        if (! $product) {
            unset($lines[$key]);
            $this->save($lines);

            throw new CartException('This item is no longer available and was removed from your cart.');
        }

        [, $stock] = $this->stockFor($product, $lines[$key]['variant_id'] ?? null);

        if ($quantity > $stock) {
            throw new CartException($stock > 0 ? "Only {$stock} available." : 'Sorry, this item is out of stock.');
        }

        $lines[$key]['qty'] = min($quantity, self::MAX_PER_LINE);
        $this->save($lines);

        return $this->summary();
    }

    public function remove(string $key): array
    {
        $lines = $this->lines();
        unset($lines[$key]);
        $this->save($lines);

        return $this->summary();
    }

    public function clear(): array
    {
        $this->save([]);

        return $this->summary();
    }

    /* ------------------------------------------------------------------ */
    /*  Reading the cart                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Cart as plain data for the page / JSON.
     * Lines that are no longer valid (product removed, hidden, out of stock) are
     * cleaned up here and reported once in "notices".
     */
    public function summary(): array
    {
        if ($this->summaryCache !== null) {
            return $this->summaryCache;
        }

        $lines = $this->lines();

        if ($lines === []) {
            return $this->summaryCache = $this->emptySummary();
        }

        $products = $this->purchasable()
            ->whereIn('id', array_unique(array_column($lines, 'product_id')))
            ->with('coverImage')
            ->get()
            ->keyBy('id');

        $variantIds = array_filter(array_column($lines, 'variant_id'));
        $variants   = $variantIds ? ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id') : collect();

        $items   = [];
        $notices = [];
        $changed = false;

        foreach ($lines as $key => $line) {
            $product = $products->get($line['product_id']);
            $variant = ! empty($line['variant_id']) ? $variants->get($line['variant_id']) : null;

            $invalid = ! $product
                || ($product->has_variants && (! $variant || (int) $variant->product_id !== (int) $product->id));

            if ($invalid) {
                unset($lines[$key]);
                $changed   = true;
                $notices[] = 'An item in your cart is no longer available and was removed.';
                continue;
            }

            if (! $product->has_variants) {
                $variant = null;
            }

            $stock = (int) ($variant ? $variant->stock : $product->stock);
            $qty   = (int) $line['qty'];

            if ($stock < 1) {
                unset($lines[$key]);
                $changed   = true;
                $notices[] = "{$product->name} is out of stock and was removed from your cart.";
                continue;
            }

            if ($qty > $stock) {
                $qty                = $stock;
                $lines[$key]['qty'] = $stock;
                $changed            = true;
                $notices[]          = "The quantity of {$product->name} was reduced to {$stock} because of limited stock.";
            }

            $price = (float) ($variant ? $variant->price : $product->price);

            $items[] = [
                'key'           => $key,
                'product_id'    => $product->id,
                'variant_id'    => $variant?->id,
                'name'          => $product->name,
                'variant_title' => $variant?->title,
                'image'         => $product->coverImage?->url ?? asset('img/fruite-item-1.jpg'),
                'url'           => route('frontend.product-detail', $product),
                'price'         => round($price, 2),
                'qty'           => $qty,
                'stock'         => $stock,
                'line_total'    => round($price * $qty, 2),
            ];
        }

        if ($changed) {
            $this->save($lines);
        }

        return $this->summaryCache = [
            'count'    => (int) array_sum(array_column($items, 'qty')),
            'subtotal' => round((float) array_sum(array_column($items, 'line_total')), 2),
            'items'    => $items,
            'notices'  => array_values(array_unique($notices)),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Internals                                                         */
    /* ------------------------------------------------------------------ */

    /** Products a customer is allowed to buy (same rules as the product page). */
    private function purchasable()
    {
        return Product::query()
            ->where('status', 'active')
            ->whereHas('shop', fn ($q) => $q->where('status', 'active'))
            ->whereHas('category', fn ($q) => $q->where('status', 'active'));
    }

    /** @return array{0: ?ProductVariant, 1: int} the chosen variant (if any) and how many are in stock */
    private function stockFor(Product $product, ?int $variantId): array
    {
        if (! $product->has_variants) {
            return [null, (int) $product->stock];
        }

        if (! $variantId) {
            throw new CartException('Please choose your options first.', route('frontend.product-detail', $product));
        }

        $variant = ProductVariant::where('product_id', $product->id)->find($variantId);

        if (! $variant) {
            throw new CartException('That option is no longer available.');
        }

        return [$variant, (int) $variant->stock];
    }

    private function lineKey(int $productId, ?int $variantId): string
    {
        return 'p' . $productId . ($variantId ? '-v' . $variantId : '');
    }

    private function lines(): array
    {
        $lines = session()->get(self::SESSION_KEY, []);

        if (! is_array($lines)) {
            return [];
        }

        return array_filter($lines, fn ($line) => is_array($line) && isset($line['product_id'], $line['qty']));
    }

    private function save(array $lines): void
    {
        session()->put(self::SESSION_KEY, $lines);
        $this->summaryCache = null;
    }

    private function emptySummary(): array
    {
        return ['count' => 0, 'subtotal' => 0.0, 'items' => [], 'notices' => []];
    }
}
