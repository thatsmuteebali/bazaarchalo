<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Throwable;

class ProductStockController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  FULL INVENTORY HISTORY (filters, totals, pagination, CSV export)  */
    /* ------------------------------------------------------------------ */
    public function index(Request $request, Product $product)
    {
        $this->ownProduct($product);

        // read filters defensively: anything invalid is simply ignored
        $variantId = $product->has_variants ? (int) $request->query('variant') : 0;
        $reason = in_array($request->query('reason'), InventoryMovement::REASONS, true) ? $request->query('reason') : null;
        $type = in_array($request->query('type'), ['in', 'out'], true) ? $request->query('type') : null;
        $from = $this->parseDate($request->query('from'));
        $to = $this->parseDate($request->query('to'));

        $query = InventoryMovement::query()
            ->where('product_id', $product->id)
            ->when($variantId > 0, fn($q) => $q->where('product_variant_id', $variantId))
            ->when($reason, fn($q) => $q->where('reason', $reason))
            ->when($type === 'in', fn($q) => $q->where('quantity_change', '>', 0))
            ->when($type === 'out', fn($q) => $q->where('quantity_change', '<', 0))
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to->copy()->endOfDay()));

        if ($request->boolean('export')) {
            return $this->export($query, $product);
        }

        $totals = (clone $query)->selectRaw(
            'COUNT(*) as entries,
             COALESCE(SUM(CASE WHEN quantity_change > 0 THEN quantity_change ELSE 0 END), 0) as added,
             COALESCE(SUM(CASE WHEN quantity_change < 0 THEN -quantity_change ELSE 0 END), 0) as removed'
        )->first();

        $movements = $query
            ->with('user:id,name')
            ->latest('created_at')->latest('id')
            ->paginate(15)
            ->withQueryString();

        $variants = $product->has_variants
            ? $product->variants()->orderBy('title')->get(['id', 'title'])
            : collect();

        return view('seller.products.inventory', compact(
            'product',
            'movements',
            'variants',
            'totals',
            'variantId',
            'reason',
            'type',
            'from',
            'to'
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  ADD / REMOVE STOCK                                                */
    /* ------------------------------------------------------------------ */
    public function store(Request $request, Product $product, StockService $stock)
    {
        $this->ownProduct($product);

        $data = $request->validate([
            'product_variant_id' => [
                Rule::requiredIf($product->has_variants),
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $product->id),
            ],
            'action' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:restock,return,damaged,correction',
            'note' => 'nullable|string|max:255',
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

    /* ------------------------------------------------------------------ */
    /*  HELPERS                                                           */
    /* ------------------------------------------------------------------ */
    private function ownProduct(Product $product): void
    {
        abort_unless((int) $product->seller_id === (int) auth()->id(), 403);
    }

    private function parseDate($value): ?Carbon
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (Throwable $e) {
            return null;
        }
    }

    private function export($query, Product $product)
    {
        $rows = (clone $query)->with('user:id,name')->latest('created_at')->latest('id')->get();
        $filename = 'inventory-history-' . $product->id . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Item', 'Reason', 'Change', 'Stock before', 'Stock after', 'By', 'Note'], ',', '"', '\\');

            foreach ($rows as $m) {
                fputcsv($out, [
                    $m->created_at->format('Y-m-d H:i:s'),
                    $this->csvSafe($m->variant_title ?? 'Product'),
                    $m->reason,
                    $m->quantity_change,
                    $m->stock_before,
                    $m->stock_after,
                    $this->csvSafe($m->user->name ?? ''),
                    $this->csvSafe($m->note),
                ], ',', '"', '\\');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /** Stops spreadsheet formulas (=, +, -, @) in user text from being executed when the CSV is opened. */
    private function csvSafe(?string $value): string
    {
        $value = (string) $value;

        return $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)
            ? "'" . $value
            : $value;
    }
}
