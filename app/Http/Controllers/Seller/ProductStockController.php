<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class ProductStockController extends Controller
{
    public function store(Request $request, Product $product, StockService $stock)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        $data = $request->validate([
            'product_variant_id' => [
                Rule::requiredIf($product->has_variants),
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $product->id),
            ],
            'action'   => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason'   => 'required|in:restock,return,damaged,correction',
            'note'     => 'nullable|string|max:255',
        ]);

        $variant = $product->has_variants
            ? $product->variants()->findOrFail($data['product_variant_id'])
            : null;

        $change = $data['action'] === 'add' ? $data['quantity'] : -$data['quantity'];

        try {
            $stock->adjust($product, $variant, $change, $data['reason'], $data['note'] ?? null, $request->user()->id);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Stock updated.');
    }
}