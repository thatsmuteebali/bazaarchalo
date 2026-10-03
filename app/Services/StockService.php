<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The ONLY place where product / variant stock is changed.
 */
class StockService
{
    /**
     * Change stock by a signed amount (+ add, - remove) and record the history.
     *
     * @throws InvalidArgumentException when the change is invalid or stock would go below zero
     */
    public function adjust(
        Product $product,
        ?ProductVariant $variant,
        int $change,
        string $reason,
        ?string $note = null,
        ?int $userId = null,
        ?Model $reference = null
    ): InventoryMovement {
        if ($change === 0) {
            throw new InvalidArgumentException('Quantity cannot be zero.');
        }

        if (! in_array($reason, InventoryMovement::REASONS, true)) {
            throw new InvalidArgumentException('Invalid reason.');
        }

        return DB::transaction(function () use ($product, $variant, $change, $reason, $note, $userId, $reference) {
            // Always lock product first, then variant (same order everywhere = no deadlocks)
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            if ($product->has_variants) {
                if (! $variant || $variant->product_id !== $product->id) {
                    throw new InvalidArgumentException('Please choose a variant.');
                }
                $variant = ProductVariant::whereKey($variant->id)->lockForUpdate()->firstOrFail();
                $before  = $variant->stock;
            } else {
                $variant = null;
                $before  = $product->stock;
            }

            $after = $before + $change;

            if ($after < 0) {
                throw new InvalidArgumentException("Not enough stock. Current stock: {$before}.");
            }

            if ($variant) {
                $variant->update(['stock' => $after]);
                $product->update(['stock' => (int) $product->variants()->sum('stock')]);
            } else {
                $product->update(['stock' => $after]);
            }

            return InventoryMovement::create([
                'product_id'         => $product->id,
                'product_variant_id' => $variant?->id,
                'variant_title'      => $variant?->title,
                'user_id'            => $userId,
                'reason'             => $reason,
                'quantity_change'    => $change,
                'stock_before'       => $before,
                'stock_after'        => $after,
                'note'               => $note,
                'reference_type'     => $reference ? $reference->getMorphClass() : null,
                'reference_id'       => $reference?->getKey(),
            ]);
        });
    }

    /**
     * Stock-take: set the exact quantity (recorded as a "correction").
     */
    public function setTo(Product $product, ?ProductVariant $variant, int $quantity, ?string $note = null, ?int $userId = null): ?InventoryMovement
    {
        $current = $product->has_variants ? ($variant?->fresh()->stock ?? 0) : $product->fresh()->stock;
        $diff    = $quantity - $current;

        return $diff === 0
            ? null
            : $this->adjust($product, $variant, $diff, 'correction', $note, $userId);
    }
}