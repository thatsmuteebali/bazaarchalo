{{-- Adjust-stock modal. Needs $product. Open it with a button that has
     data-bs-toggle="modal" data-bs-target="#stockModal" and data-variant-id / data-label / data-stock. --}}
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
                <div class="mb-3 text-muted" style="font-size:13px;">Current stock: <strong
                        class="js-stock-current"></strong></div>

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
                        <input type="number" name="quantity" min="1" step="1" required
                            class="form-control-custom">
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
