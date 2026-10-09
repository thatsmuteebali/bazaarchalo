@extends('layouts.seller')

@section('customCss')
    @include('seller.products._form_css')
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Product</h1>
            <p class="page-subtitle">{{ $product->name }}</p>
        </div>
        <a href="{{ route('seller.products.index') }}" class="btn-custom btn-custom-light">Back to Products</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            @include('seller.products._form', ['product' => $product])

            {{-- INVENTORY (separate from the product form on purpose) --}}
            <div class="card border-light shadow-sm p-4 mb-4" id="inventory">
                <div class="section-title">Inventory</div>

                @if ($errors->hasAny(['quantity', 'action', 'reason', 'note', 'product_variant_id']))
                    <div class="alert alert-danger">
                        @foreach ($errors->only(['quantity', 'action', 'reason', 'note', 'product_variant_id']) as $messages)
                            @foreach ((array) $messages as $msg)
                                <div>{{ $msg }}</div>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                <div class="variants-table-wrap mb-4">
                    <table class="variants-table" style="min-width:0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($product->has_variants)
                                @foreach ($product->variants as $variant)
                                    <tr>
                                        <td class="variant-name">{{ $variant->title }}</td>
                                        <td>{{ $variant->sku ?: '—' }}</td>
                                        <td><span class="stock-static {{ $variant->stock < 1 ? 'stock-zero' : '' }}">{{ $variant->stock }}</span></td>
                                        <td class="text-end">
                                            <button type="button" class="btn-custom btn-custom-light"
                                                data-bs-toggle="modal" data-bs-target="#stockModal"
                                                data-variant-id="{{ $variant->id }}"
                                                data-label="{{ $variant->title }}"
                                                data-stock="{{ $variant->stock }}">Adjust stock</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="variant-name">{{ $product->name }}</td>
                                    <td>—</td>
                                    <td><span class="stock-static {{ $product->stock < 1 ? 'stock-zero' : '' }}">{{ $product->stock }}</span></td>
                                    <td class="text-end">
                                        <button type="button" class="btn-custom btn-custom-light"
                                            data-bs-toggle="modal" data-bs-target="#stockModal"
                                            data-variant-id="" data-label="{{ $product->name }}"
                                            data-stock="{{ $product->stock }}">Adjust stock</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="fw-semibold mb-2">Recent stock history</div>
                @if ($movements->isEmpty())
                    <div class="image-hint">No stock changes yet.</div>
                @else
                    <div class="variants-table-wrap">
                        <table class="variants-table" style="min-width:640px">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Item</th>
                                    <th>Reason</th>
                                    <th>Change</th>
                                    <th>Stock after</th>
                                    <th>By</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movements as $m)
                                    <tr>
                                        <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $m->variant_title ?? 'Product' }}</td>
                                        <td>{{ ucfirst($m->reason) }}</td>
                                        <td class="{{ $m->quantity_change > 0 ? 'move-plus' : 'move-minus' }}">
                                            {{ $m->quantity_change > 0 ? '+' : '' }}{{ $m->quantity_change }}</td>
                                        <td>{{ $m->stock_after }}</td>
                                        <td>{{ $m->user->name ?? '—' }}</td>
                                        <td>{{ $m->note ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- STOCK MODAL --}}
    <div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('seller.products.stock.store', $product) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust stock — <span class="js-stock-label"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_variant_id" value="">
                    <div class="image-hint mb-3">Current stock: <strong class="js-stock-current"></strong></div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label-custom">Action</label>
                            <select name="action" class="form-select-custom">
                                <option value="add">Add stock</option>
                                <option value="remove">Remove stock</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label-custom">Quantity</label>
                            <input type="number" name="quantity" min="1" step="1" required class="form-control-custom">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label-custom">Reason</label>
                            <select name="reason" class="form-select-custom"></select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Note (optional)</label>
                            <input type="text" name="note" maxlength="255" class="form-control-custom">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-custom btn-custom-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-custom btn-custom-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('customjs')
    @include('seller.products._form_js', ['product' => $product])

    <script>
        (function() {
            const modal = document.getElementById('stockModal');
            if (!modal) return;

            const reasons = {
                add: [
                    ['restock', 'Restock'],
                    ['return', 'Customer return'],
                    ['correction', 'Correction']
                ],
                remove: [
                    ['damaged', 'Damaged / lost'],
                    ['correction', 'Correction']
                ]
            };

            const actionSel = modal.querySelector('[name="action"]');
            const reasonSel = modal.querySelector('[name="reason"]');

            function fillReasons() {
                reasonSel.innerHTML = reasons[actionSel.value]
                    .map(r => `<option value="${r[0]}">${r[1]}</option>`).join('');
            }
            actionSel.addEventListener('change', fillReasons);
            fillReasons();

            modal.addEventListener('show.bs.modal', e => {
                const btn = e.relatedTarget;
                if (!btn) return;

                modal.querySelector('[name="product_variant_id"]').value = btn.dataset.variantId || '';
                modal.querySelector('.js-stock-label').textContent = btn.dataset.label;
                modal.querySelector('.js-stock-current').textContent = btn.dataset.stock;
                modal.querySelector('[name="quantity"]').value = '';
                modal.querySelector('[name="note"]').value = '';
            });
        })();
    </script>
@endsection
