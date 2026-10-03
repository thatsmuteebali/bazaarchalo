@extends('layouts.seller')

@section('customCss')
    <style>
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }

        .image-tile {
            position: relative;
            aspect-ratio: 1 / 1;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            overflow: hidden;
            transition: border-color .2s, background .2s;
        }

        .image-tile:hover {
            border-color: #198754;
            background: #f0fdf4;
            color: #198754;
        }

        .image-tile i.bi {
            font-size: 26px;
        }

        .image-tile input[type="file"] {
            display: none;
        }

        .image-tile.filled {
            border-style: solid;
            border-color: #e2e8f0;
            cursor: default;
            background: #fff;
        }

        .image-tile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-tile .remove-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 26px;
            height: 26px;
            border: 0;
            border-radius: 50%;
            background: rgba(15, 23, 42, .75);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }

        .image-tile .remove-btn:hover {
            background: #dc3545;
        }

        .image-tile .cover-badge {
            position: absolute;
            left: 6px;
            bottom: 6px;
            padding: 2px 8px;
            border-radius: 20px;
            background: #198754;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
        }

        .image-hint {
            font-size: 12px;
            color: #94a3b8;
        }

        .image-error {
            font-size: 13px;
            color: #dc3545;
            margin-top: 8px;
            display: none;
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ---------- Variants ---------- */
        #variantsFieldset {
            border: 0;
            padding: 0;
            margin: 0;
            min-width: 0;
        }

        .option-row {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #f8fafc;
        }

        .option-head {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .icon-btn {
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            border-radius: 8px;
            width: 40px;
            flex-shrink: 0;
            cursor: pointer;
        }

        .icon-btn:hover {
            color: #dc3545;
            border-color: #dc3545;
        }

        .chip-box {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
            min-height: 42px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e8f5ee;
            color: #146c43;
            border-radius: 20px;
            padding: 3px 6px 3px 12px;
            font-size: 13px;
            font-weight: 500;
        }

        .chip-x {
            border: 0;
            background: rgba(20, 108, 67, .15);
            color: #146c43;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            line-height: 1;
            font-size: 13px;
            cursor: pointer;
            padding: 0;
        }

        .chip-x:hover {
            background: #dc3545;
            color: #fff;
        }

        .chip-input {
            border: 0;
            outline: 0;
            flex: 1;
            min-width: 140px;
            font-size: 14px;
            background: transparent;
        }

        .variants-table-wrap {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .variants-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 720px;
        }

        .variants-table th {
            background: #f8fafc;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 12px;
            white-space: nowrap;
        }

        .variants-table td {
            padding: 8px 12px;
            border-top: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .variants-table td input {
            width: 100%;
            min-width: 90px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 14px;
        }

        .variants-table td input:focus {
            outline: 0;
            border-color: #198754;
        }

        .variant-name {
            font-weight: 600;
            white-space: nowrap;
        }

        .bulk-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin: 14px 0 10px;
            font-size: 13px;
            color: #64748b;
        }

        .bulk-bar input {
            width: 120px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 14px;
        }

        .link-btn {
            border: 0;
            background: none;
            color: #198754;
            font-size: 13px;
            padding: 0;
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Product</h1>
            <p class="page-subtitle">Create a product for your shop.</p>
        </div>
        <a href="{{ route('seller.products.index') }}" class="btn-custom btn-custom-light">Back to Products</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" id="productForm"
                novalidate>
                @csrf

                {{-- BASIC INFO --}}
                <div class="card border-light shadow-sm p-4 mb-4">
                    <div class="section-title">Basic Information</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label-custom">Product Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="form-control-custom @error('name') is-invalid-custom @enderror">
                            @error('name')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label-custom">Category</label>
                            <select name="category_id" id="category"
                                class="form-select-custom @error('category_id') is-invalid-custom @enderror">
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select
                                    category...
                                </option>
                                @foreach ($categories ?? [] as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="shop" class="form-label-custom">Shop</label>
                            <select name="shop_id" id="shop"
                                class="form-select-custom @error('shop_id') is-invalid-custom @enderror">
                                <option value="" disabled {{ old('shop_id') ? '' : 'selected' }}>
                                    Select shop...
                                </option>

                                @foreach ($shops ?? [] as $shop)
                                    <option value="{{ $shop->id }}"
                                        {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('shop_id')
                                <div class="form-feedback-custom invalid-custom">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="collection" class="form-label-custom">Collection</label>
                            <select name="collection_id" id="collection"
                                class="form-select-custom @error('collection_id') is-invalid-custom @enderror">
                                <option value="">No collection</option>
                                @foreach ($collections ?? [] as $collection)
                                    <option value="{{ $collection->id }}"
                                        {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                        {{ $collection->name }}</option>
                                @endforeach
                            </select>
                            @error('collection_id')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="short_description" class="form-label-custom">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="2" maxlength="255"
                                class="form-control-custom @error('short_description') is-invalid-custom @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-1">
                            <label for="description" class="form-label-custom">Description</label>
                            <textarea name="description" id="description" rows="6"
                                class="form-control-custom summernote @error('description') is-invalid-custom @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- IMAGES --}}
                <div class="card border-light shadow-sm p-4 mb-4">
                    <div class="section-title">Product Images</div>
                    <div class="image-hint mb-3">
                        Click a box to choose an image — the next box appears automatically. The first image is used as
                        the cover. Up to <span id="maxImagesLabel">8</span> images, JPG/PNG/WEBP, max 2 MB each.
                    </div>
                    <div class="image-grid" id="imageGrid"></div>
                    <div class="image-error" id="imageError"></div>
                    @error('images')
                        <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}</div>
                    @enderror
                    @error('images.*')
                        <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}</div>
                    @enderror
                </div>

                {{-- PRICING & INVENTORY --}}
                <div class="card border-light shadow-sm p-4 mb-4">
                    <div class="section-title">Pricing &amp; Inventory</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label-custom">Price</label>
                            <input type="number" step="0.01" min="0" name="price" id="price"
                                value="{{ old('price') }}"
                                class="form-control-custom @error('price') is-invalid-custom @enderror">
                            @error('price')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="compare_price" class="form-label-custom">Compare Price</label>
                            <input type="number" step="0.01" min="0" name="compare_price" id="compare_price"
                                value="{{ old('compare_price') }}"
                                class="form-control-custom @error('compare_price') is-invalid-custom @enderror">
                            <div class="image-hint mt-1">Original price shown as struck-through.</div>
                            @error('compare_price')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label-custom">Stock Inventory</label>
                            <input type="number" min="0" step="1" name="stock" id="stock"
                                value="{{ old('stock', 0) }}"
                                class="form-control-custom @error('stock') is-invalid-custom @enderror">
                            <div class="image-hint mt-1" id="stockHint" style="display:none">Managed by variants below.
                            </div>
                            @error('stock')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- VARIANTS --}}
                <div class="card border-light shadow-sm p-4 mb-4">
                    <div class="section-title">Variants</div>

                    <input type="hidden" name="has_variants" value="0">
                    <div class="form-switch-custom mb-3">
                        <input name="has_variants" value="1" class="form-switch-input-custom" type="checkbox"
                            id="has_variants" {{ old('has_variants') ? 'checked' : '' }}>
                        <label class="form-switch-label" for="has_variants">This product has multiple options, like size
                            or
                            color</label>
                    </div>

                    <div id="variantsPanel" style="display:none">
                        <fieldset id="variantsFieldset" disabled>
                            <div class="image-hint mb-3">
                                Add options (e.g. Size, Color) and type their values, pressing Enter after each. A variant
                                is created for every combination.
                            </div>

                            <div id="optionsBox"></div>
                            <button type="button" class="btn-custom btn-custom-light mb-4" id="addOptionBtn">
                                <i class="bi bi-plus-lg"></i> Add another option
                            </button>

                            <div id="variantsSummary" class="fw-semibold mb-1"></div>

                            <div class="bulk-bar" id="bulkBar" style="display:none">
                                <span>Apply to all:</span>
                                <input type="number" step="0.01" min="0" id="bulkPrice" placeholder="Price">
                                <input type="number" step="1" min="0" id="bulkStock" placeholder="Stock">
                                <button type="button" class="btn-custom btn-custom-light" id="bulkApply">Apply</button>
                                <button type="button" class="link-btn" id="restoreBtn" style="display:none">Restore
                                    removed
                                    variants</button>
                            </div>

                            <div class="variants-table-wrap" id="variantsTableWrap"></div>
                        </fieldset>
                    </div>

                    @error('variants')
                        <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}</div>
                    @enderror
                    @error('variants.*')
                        <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}</div>
                    @enderror
                </div>

                {{-- VISIBILITY --}}
                <div class="card border-light shadow-sm p-4 mb-4">
                    <div class="section-title">Visibility</div>
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label-custom">Status</label>
                            <select name="status" id="status"
                                class="form-select-custom @error('status') is-invalid-custom @enderror">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="form-feedback-custom invalid-custom"><i class="bi bi-exclamation-circle-fill"></i>
                                    {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <input type="hidden" name="is_featured" value="0">
                            <div class="form-switch-custom">
                                <input name="is_featured" value="1" class="form-switch-input-custom"
                                    type="checkbox" id="is_featured" checked>
                                <label class="form-switch-label" for="is_featured">Feature this product</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button class="btn-custom btn-custom-primary" type="submit">Create Product</button>
                    <a href="{{ route('seller.products.index') }}" class="btn-custom btn-custom-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
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

            // Don't submit empty file inputs
            form.addEventListener('submit', () => {
                grid.querySelectorAll('.image-tile:not(.filled) input').forEach(i => i.disabled = true);
            });

            addSlot();
        })();

        /* =========================================================
         * VARIANTS BUILDER
         * ======================================================= */
        (function() {

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
            const stockInput = document.getElementById('stock');
            const stockHint = document.getElementById('stockHint');
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

            // Restore after a failed validation
            const oldOptions = @json(old('options', []));
            const oldVariants = @json(old('variants', []));

            Object.values(oldOptions || {}).forEach(o => {
                options.push({
                    name: o.name || '',
                    values: Object.values(o.values || [])
                });
            });
            const oldList = Object.values(oldVariants || {});
            oldList.forEach(v => {
                variantData[v.title] = {
                    sku: v.sku || '',
                    price: v.price ?? '',
                    compare_price: v.compare_price ?? '',
                    stock: v.stock ?? 0
                };
            });

            // ---- helpers ----
            const comboKey = combo => combo.join(' / ');

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
                const act = activeOptions();

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

                const headers = act.map(o => `<th>${esc(o.name)}</th>`).join('');

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
                            <td><input type="number" step="1" min="0" class="v-field" data-field="stock" name="variants[${idx}][stock]" value="${esc(d.stock)}"></td>
                            <td><button type="button" class="icon-btn v-remove" style="height:34px" title="Remove variant"><i class="bi bi-x-lg"></i></button></td>
                        </tr>`;
                }).join('');

                tableWrap.innerHTML = `
                    <table class="variants-table">
                        <thead>
                            <tr>${headers ? '<th>Variant</th>' : ''}<th>SKU</th><th>Price</th><th>Compare Price</th><th>Stock</th><th></th></tr>
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
                removed.add(btn.closest('tr').dataset.key);
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
                    if (s !== '') variantData[key].stock = s;
                });
                renderVariants();
            });

            // ---- toggle ----
            function syncToggle() {
                const on = toggle.checked;
                panel.style.display = on ? '' : 'none';
                fieldset.disabled = !on; // disabled inputs are not submitted
                stockInput.disabled = on;
                stockHint.style.display = on ? '' : 'none';

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
            // If variants were restored from old input, re-mark the combinations the seller had deleted
            if (oldList.length) {
                const kept = new Set(oldList.map(v => v.title));
                combinations().forEach(c => {
                    if (!kept.has(comboKey(c))) removed.add(comboKey(c));
                });
            }
            renderVariants();
            syncToggle();
        })();
    </script>
@endsection
