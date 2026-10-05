@php
    $isEdit = isset($product) && $product;

    $cfg = [
        'isEdit'  => (bool) $isEdit,
        // after a failed validation the submitted values win over the saved ones
        'options' => old('options', $isEdit
            ? $product->options->sortBy('position')->map(fn ($o) => ['name' => $o->name, 'values' => $o->values])->values()->all()
            : []),
        'variants' => old('variants', $isEdit
            ? $product->variants->map(fn ($v) => [
                'title' => $v->title, 'sku' => $v->sku, 'price' => $v->price,
                'compare_price' => $v->compare_price, 'stock' => $v->stock,
            ])->values()->all()
            : []),
        // title => current stock of variants that already exist in the database
        'existingStock' => $isEdit ? $product->variants->pluck('stock', 'title')->all() : [],
    ];
@endphp

<script>
    const PRODUCT_FORM = @json($cfg);

    /* =========================================================
     * IMAGE PICKER
     * ======================================================= */
    (function() {
        const MAX_IMAGES = 8;
        const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
        const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

        const grid = document.getElementById('imageGrid');
        const errorBox = document.getElementById('imageError');
        const form = document.getElementById('productForm');
        document.getElementById('maxImagesLabel').textContent = MAX_IMAGES;

        const filledCount = () => grid.querySelectorAll('.image-tile.filled').length;
        const hasEmptySlot = () => grid.querySelector('.image-tile:not(.filled)') !== null;

        function showError(msg) {
            errorBox.textContent = msg;
            errorBox.style.display = msg ? 'block' : 'none';
        }

        function refreshCover() {
            grid.querySelectorAll('.cover-badge').forEach(b => b.remove());
            const first = grid.querySelector('.image-tile.filled');
            if (first) {
                const badge = document.createElement('span');
                badge.className = 'cover-badge';
                badge.textContent = 'Cover';
                first.appendChild(badge);
            }
        }

        function addSlot() {
            if (filledCount() >= MAX_IMAGES || hasEmptySlot()) return;

            const tile = document.createElement('label');
            tile.className = 'image-tile';
            tile.innerHTML = `
                <i class="bi bi-cloud-arrow-up"></i>
                <span>Add image</span>
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp">
            `;

            const input = tile.querySelector('input');
            input.addEventListener('change', () => handleFile(tile, input));
            grid.appendChild(tile);
        }

        function handleFile(tile, input) {
            const file = input.files[0];
            if (!file) return;

            if (!ALLOWED.includes(file.type)) {
                input.value = '';
                return showError(`"${file.name}" is not a supported image type.`);
            }
            if (file.size > MAX_SIZE) {
                input.value = '';
                return showError(`"${file.name}" is larger than 2 MB.`);
            }
            showError('');

            const reader = new FileReader();
            reader.onload = e => {
                tile.classList.add('filled');
                tile.innerHTML = '';
                tile.appendChild(input); // keep the input so the file is submitted

                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = file.name;

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'remove-btn';
                remove.title = 'Remove';
                remove.innerHTML = '<i class="bi bi-x-lg"></i>';
                remove.addEventListener('click', ev => {
                    ev.preventDefault();
                    tile.remove();
                    showError('');
                    addSlot();
                    refreshCover();
                });

                tile.append(img, remove);
                refreshCover();
                addSlot(); // move on to the next image
            };
            reader.readAsDataURL(file);
        }

        // Images that are already saved (edit page): removing one marks it for deletion
        grid.querySelectorAll('.image-tile.existing').forEach(tile => {
            tile.querySelector('.remove-btn').addEventListener('click', () => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'remove_images[]';
                hidden.value = tile.dataset.id;
                grid.appendChild(hidden);

                tile.remove();
                showError('');
                addSlot();
                refreshCover();
            });
        });

        // Don't submit empty file inputs
        form.addEventListener('submit', () => {
            grid.querySelectorAll('.image-tile:not(.filled) input').forEach(i => i.disabled = true);
        });

        refreshCover();
        addSlot();
    })();

    /* =========================================================
     * VARIANTS BUILDER
     * ======================================================= */
    (function() {
        const CFG = PRODUCT_FORM;
        const IS_EDIT = CFG.isEdit;

        const form = document.getElementById('productForm');
        const toggle = document.getElementById('has_variants');
        const panel = document.getElementById('variantsPanel');
        const fieldset = document.getElementById('variantsFieldset');
        const optionsBox = document.getElementById('optionsBox');
        const addOptionBtn = document.getElementById('addOptionBtn');
        const tableWrap = document.getElementById('variantsTableWrap');
        const summary = document.getElementById('variantsSummary');
        const bulkBar = document.getElementById('bulkBar');
        const restoreBtn = document.getElementById('restoreBtn');
        const stockInput = document.getElementById('stock'); // not on the edit page
        const stockHint = document.getElementById('stockHint'); // not on the edit page
        const priceInput = document.getElementById('price');

        const esc = s => String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        // ---- state ----
        let options = []; // [{ name, values: [] }]
        const variantData = {}; // "Red / S" => { sku, price, compare_price, stock }
        const removed = new Set(); // combination keys the seller deleted

        Object.values(CFG.options || {}).forEach(o => {
            options.push({
                name: o.name || '',
                values: Object.values(o.values || [])
            });
        });

        const initList = Object.values(CFG.variants || {});
        initList.forEach(v => {
            variantData[v.title] = {
                sku: v.sku || '',
                price: v.price ?? '',
                compare_price: v.compare_price ?? '',
                stock: v.stock ?? 0
            };
        });

        // ---- helpers ----
        const comboKey = combo => combo.join(' / ');
        const isExisting = key => IS_EDIT && Object.prototype.hasOwnProperty.call(CFG.existingStock || {}, key);

        function activeOptions() {
            return options.filter(o => o.name.trim() && o.values.length);
        }

        function combinations() {
            const act = activeOptions();
            if (!act.length) return [];
            return act.reduce((acc, opt) => {
                const next = [];
                acc.forEach(prefix => opt.values.forEach(v => next.push([...prefix, v])));
                return next;
            }, [
                []
            ]);
        }

        // ---- options UI ----
        function renderOptions(focusIndex = null) {
            optionsBox.innerHTML = options.map((o, i) => `
                <div class="option-row" data-i="${i}">
                    <div class="option-head">
                        <input type="text" class="form-control-custom opt-name"
                            name="options[${i}][name]" placeholder="Option name (e.g. Size, Color)"
                            value="${esc(o.name)}">
                        <button type="button" class="icon-btn opt-remove" title="Remove option">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="chip-box">
                        ${o.values.map((v, vi) => `
                            <span class="chip">${esc(v)}
                                <button type="button" class="chip-x" data-vi="${vi}" title="Remove">&times;</button>
                                <input type="hidden" name="options[${i}][values][]" value="${esc(v)}">
                            </span>`).join('')}
                        <input type="text" class="chip-input" placeholder="Type a value, press Enter">
                    </div>
                </div>`).join('');

            if (focusIndex !== null) {
                const row = optionsBox.querySelector(`.option-row[data-i="${focusIndex}"] .chip-input`);
                if (row) row.focus();
            }
        }

        function addValues(i, raw, keepFocus) {
            const parts = String(raw).split(',').map(s => s.trim()).filter(Boolean);
            if (!parts.length) return;
            parts.forEach(p => {
                const exists = options[i].values.some(v => v.toLowerCase() === p.toLowerCase());
                if (!exists) options[i].values.push(p);
            });
            renderOptions(keepFocus ? i : null);
            renderVariants();
        }

        optionsBox.addEventListener('input', e => {
            if (e.target.classList.contains('opt-name')) {
                const i = +e.target.closest('.option-row').dataset.i;
                options[i].name = e.target.value;
                renderVariants();
            }
        });

        optionsBox.addEventListener('keydown', e => {
            if (!e.target.classList.contains('chip-input')) return;
            const i = +e.target.closest('.option-row').dataset.i;

            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addValues(i, e.target.value, true);
            } else if (e.key === 'Backspace' && !e.target.value && options[i].values.length) {
                options[i].values.pop();
                renderOptions(i);
                renderVariants();
            }
        });

        optionsBox.addEventListener('focusout', e => {
            if (e.target.classList.contains('chip-input') && e.target.value.trim()) {
                const i = +e.target.closest('.option-row').dataset.i;
                addValues(i, e.target.value, false);
            }
        });

        optionsBox.addEventListener('click', e => {
            const row = e.target.closest('.option-row');
            if (!row) return;
            const i = +row.dataset.i;

            if (e.target.closest('.chip-x')) {
                options[i].values.splice(+e.target.closest('.chip-x').dataset.vi, 1);
                renderOptions();
                renderVariants();
            } else if (e.target.closest('.opt-remove')) {
                options.splice(i, 1);
                renderOptions();
                renderVariants();
            }
        });

        addOptionBtn.addEventListener('click', () => {
            options.push({
                name: '',
                values: []
            });
            renderOptions();
            const names = optionsBox.querySelectorAll('.opt-name');
            names[names.length - 1].focus();
        });

        // ---- variants table ----
        function renderVariants() {
            const list = combinations();

            if (!list.length) {
                summary.textContent = '';
                bulkBar.style.display = 'none';
                tableWrap.style.display = 'none';
                return;
            }

            const visible = list.filter(c => !removed.has(comboKey(c)));
            summary.textContent = `${visible.length} variant${visible.length === 1 ? '' : 's'}`;
            bulkBar.style.display = '';
            tableWrap.style.display = '';
            restoreBtn.style.display = removed.size ? '' : 'none';

            const rows = visible.map((combo, idx) => {
                const key = comboKey(combo);
                if (!variantData[key]) {
                    variantData[key] = {
                        sku: '',
                        price: priceInput.value || '',
                        compare_price: '',
                        stock: 0
                    };
                }
                const d = variantData[key];
                const k = esc(key);

                // existing variants show their stock (changed through the Inventory section);
                // new variants get a field for their starting stock
                const stockCell = isExisting(key) ?
                    `<td><span class="stock-static" title="Change stock from the Inventory section">${esc(CFG.existingStock[key])}</span></td>` :
                    `<td><input type="number" step="1" min="0" class="v-field" data-field="stock" name="variants[${idx}][stock]" value="${esc(d.stock)}"></td>`;

                return `
                    <tr data-key="${k}">
                        <td class="variant-name">
                            ${esc(key)}
                            <input type="hidden" name="variants[${idx}][title]" value="${k}">
                            ${combo.map(v => `<input type="hidden" name="variants[${idx}][option_values][]" value="${esc(v)}">`).join('')}
                        </td>
                        <td><input type="text" class="v-field" data-field="sku" name="variants[${idx}][sku]" value="${esc(d.sku)}" placeholder="SKU"></td>
                        <td><input type="number" step="0.01" min="0" class="v-field" data-field="price" name="variants[${idx}][price]" value="${esc(d.price)}"></td>
                        <td><input type="number" step="0.01" min="0" class="v-field" data-field="compare_price" name="variants[${idx}][compare_price]" value="${esc(d.compare_price)}"></td>
                        ${stockCell}
                        <td><button type="button" class="icon-btn v-remove" style="height:34px" title="Remove variant"><i class="bi bi-x-lg"></i></button></td>
                    </tr>`;
            }).join('');

            tableWrap.innerHTML = `
                <table class="variants-table">
                    <thead>
                        <tr><th>Variant</th><th>SKU</th><th>Price</th><th>Compare Price</th><th>Stock</th><th></th></tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>`;
        }

        tableWrap.addEventListener('input', e => {
            if (!e.target.classList.contains('v-field')) return;
            const key = e.target.closest('tr').dataset.key;
            variantData[key][e.target.dataset.field] = e.target.value;
        });

        tableWrap.addEventListener('click', e => {
            const btn = e.target.closest('.v-remove');
            if (!btn) return;

            const key = btn.closest('tr').dataset.key;

            if (isExisting(key) && Number(CFG.existingStock[key]) > 0) {
                const ok = confirm(
                    `"${key}" has ${CFG.existingStock[key]} in stock.\n\n` +
                    `Removing it sets its stock to 0 (recorded in the stock history). Continue?`
                );
                if (!ok) return;
            }

            removed.add(key);
            renderVariants();
        });

        restoreBtn.addEventListener('click', () => {
            removed.clear();
            renderVariants();
        });

        document.getElementById('bulkApply').addEventListener('click', () => {
            const p = document.getElementById('bulkPrice').value;
            const s = document.getElementById('bulkStock').value;
            combinations().forEach(c => {
                const key = comboKey(c);
                if (removed.has(key) || !variantData[key]) return;
                if (p !== '') variantData[key].price = p;
                if (s !== '' && !isExisting(key)) variantData[key].stock = s;
            });
            renderVariants();
        });

        // ---- toggle ----
        function syncToggle() {
            const on = toggle.checked;
            panel.style.display = on ? '' : 'none';
            fieldset.disabled = !on; // disabled inputs are not submitted
            if (stockInput) stockInput.disabled = on;
            if (stockHint) stockHint.style.display = on ? '' : 'none';

            if (on && !options.length) {
                options.push({
                    name: '',
                    values: []
                });
                renderOptions();
            }
        }
        toggle.addEventListener('change', syncToggle);

        // Flush any half-typed value, then drop empty options before submitting
        form.addEventListener('submit', () => {
            optionsBox.querySelectorAll('.chip-input').forEach(inp => {
                if (inp.value.trim()) {
                    addValues(+inp.closest('.option-row').dataset.i, inp.value, false);
                }
            });

            // remove option rows that have no name and no values
            options = options.filter(o => o.name.trim() || o.values.length);
            renderOptions();
        });

        // ---- init ----
        renderOptions();

        // Combinations that are not in the saved / submitted variant list were deleted by the seller
        if (initList.length) {
            const kept = new Set(initList.map(v => v.title));
            combinations().forEach(c => {
                if (!kept.has(comboKey(c))) removed.add(comboKey(c));
            });
        }

        renderVariants();
        syncToggle();
    })();
</script>
