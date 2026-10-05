@php
    $isEdit = isset($product) && $product;
    $removedIds = array_map('intval', (array) old('remove_images', []));
    $imageErrors = collect($errors->getMessages())
        ->filter(fn ($m, $k) => str_starts_with($k, 'images') || str_starts_with($k, 'remove_images'))
        ->flatten()->unique();
    $variantErrors = collect($errors->getMessages())
        ->filter(fn ($m, $k) => str_starts_with($k, 'variants') || str_starts_with($k, 'options'))
        ->flatten()->unique();
    $featured = filter_var(old('is_featured', $isEdit ? $product->is_featured : false), FILTER_VALIDATE_BOOLEAN);
    $hasVariantsOn = $isEdit ? $product->has_variants : (bool) old('has_variants');
@endphp

<form method="POST"
    action="{{ $isEdit ? route('seller.products.update', $product) : route('seller.products.store') }}"
    enctype="multipart/form-data" id="productForm" novalidate>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- BASIC INFO --}}
    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="section-title">Basic Information</div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label-custom">Product Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
                    class="form-control-custom @error('name') is-invalid-custom @enderror">
                @include('seller.products._error', ['field' => 'name'])
            </div>

            <div class="col-md-6 mb-3">
                <label for="category" class="form-label-custom">Category</label>
                @php $selCategory = old('category_id', $product->category_id ?? null); @endphp
                <select name="category_id" id="category"
                    class="form-select-custom @error('category_id') is-invalid-custom @enderror">
                    <option value="" disabled {{ $selCategory ? '' : 'selected' }}>Select category...</option>
                    @foreach ($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ $selCategory == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
                @include('seller.products._error', ['field' => 'category_id'])
            </div>

            <div class="col-md-6 mb-3">
                <label for="shop" class="form-label-custom">Shop</label>
                @php $selShop = old('shop_id', $product->shop_id ?? null); @endphp
                <select name="shop_id" id="shop"
                    class="form-select-custom @error('shop_id') is-invalid-custom @enderror">
                    <option value="" disabled {{ $selShop ? '' : 'selected' }}>Select shop...</option>
                    @foreach ($shops ?? [] as $shop)
                        <option value="{{ $shop->id }}" {{ $selShop == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}</option>
                    @endforeach
                </select>
                @include('seller.products._error', ['field' => 'shop_id'])
            </div>

            <div class="col-md-6 mb-3">
                <label for="collection" class="form-label-custom">Collection</label>
                @php $selCollection = old('collection_id', $product->collection_id ?? null); @endphp
                <select name="collection_id" id="collection"
                    class="form-select-custom @error('collection_id') is-invalid-custom @enderror">
                    <option value="">No collection</option>
                    @foreach ($collections ?? [] as $collection)
                        <option value="{{ $collection->id }}" {{ $selCollection == $collection->id ? 'selected' : '' }}>
                            {{ $collection->name }}</option>
                    @endforeach
                </select>
                @include('seller.products._error', ['field' => 'collection_id'])
            </div>

            <div class="col-12 mb-3">
                <label for="short_description" class="form-label-custom">Short Description</label>
                <textarea name="short_description" id="short_description" rows="2" maxlength="255"
                    class="form-control-custom @error('short_description') is-invalid-custom @enderror">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                @include('seller.products._error', ['field' => 'short_description'])
            </div>

            <div class="col-12 mb-1">
                <label for="description" class="form-label-custom">Description</label>
                <textarea name="description" id="description" rows="6"
                    class="form-control-custom summernote @error('description') is-invalid-custom @enderror">{{ old('description', $product->description ?? '') }}</textarea>
                @include('seller.products._error', ['field' => 'description'])
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

        <div class="image-grid" id="imageGrid">
            @if ($isEdit)
                @foreach ($product->images->sortBy('sort_order') as $img)
                    @if (in_array($img->id, $removedIds, true))
                        <input type="hidden" name="remove_images[]" value="{{ $img->id }}">
                    @else
                        <div class="image-tile filled existing" data-id="{{ $img->id }}">
                            <img src="{{ $img->url }}" alt="">
                            <button type="button" class="remove-btn" title="Remove"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="image-error" id="imageError"></div>
        @foreach ($imageErrors as $msg)
            <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                {{ $msg }}</div>
        @endforeach
    </div>

    {{-- PRICING & INVENTORY --}}
    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="section-title">Pricing &amp; Inventory</div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="price" class="form-label-custom">Price</label>
                <input type="number" step="0.01" min="0" name="price" id="price"
                    value="{{ old('price', $product->price ?? '0') }}"
                    class="form-control-custom @error('price') is-invalid-custom @enderror">
                @include('seller.products._error', ['field' => 'price'])
            </div>

            <div class="col-md-4 mb-3">
                <label for="compare_price" class="form-label-custom">Compare Price</label>
                <input type="number" step="0.01" min="0" name="compare_price" id="compare_price"
                    value="{{ old('compare_price', $product->compare_price ?? '') }}"
                    class="form-control-custom @error('compare_price') is-invalid-custom @enderror">
                <div class="image-hint mt-1">Original price shown as struck-through.</div>
                @include('seller.products._error', ['field' => 'compare_price'])
            </div>

            <div class="col-md-4 mb-3">
                @if ($isEdit)
                    <label class="form-label-custom">Stock Inventory</label>
                    <div>
                        <span class="stock-static {{ $product->stock < 1 ? 'stock-zero' : '' }}">{{ $product->stock }}</span>
                    </div>
                    <div class="image-hint mt-1">Change stock from the Inventory section below.</div>
                @else
                    <label for="stock" class="form-label-custom">Stock Inventory</label>
                    <input type="number" min="0" step="1" name="stock" id="stock"
                        value="{{ old('stock', 0) }}"
                        class="form-control-custom @error('stock') is-invalid-custom @enderror">
                    <div class="image-hint mt-1" id="stockHint" style="display:none">Managed by variants below.</div>
                    @include('seller.products._error', ['field' => 'stock'])
                @endif
            </div>
        </div>
    </div>

    {{-- VARIANTS --}}
    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="section-title">Variants</div>

        <div class="form-switch-custom mb-3">
            @if ($isEdit)
                {{-- fixed after creation: no name, so it is never submitted --}}
                <input class="form-switch-input-custom" type="checkbox" id="has_variants" disabled
                    {{ $hasVariantsOn ? 'checked' : '' }}>
            @else
                <input type="hidden" name="has_variants" value="0">
                <input name="has_variants" value="1" class="form-switch-input-custom" type="checkbox"
                    id="has_variants" {{ $hasVariantsOn ? 'checked' : '' }}>
            @endif
            <label class="form-switch-label" for="has_variants">This product has multiple options, like size or
                color</label>
        </div>

        @if ($isEdit)
            <div class="image-hint mb-3">
                This cannot be changed after the product is created.
            </div>
        @endif

        <div id="variantsPanel" style="display:none">
            <fieldset id="variantsFieldset" disabled>
                <div class="image-hint mb-3">
                    Add options (e.g. Size, Color) and type their values, pressing Enter after each. A variant
                    is created for every combination.
                    @if ($isEdit)
                        Existing variants keep their stock; new variants start with the stock you type.
                    @endif
                </div>

                <div id="optionsBox"></div>
                <button type="button" class="btn-custom btn-custom-light mb-4" id="addOptionBtn">
                    <i class="bi bi-plus-lg"></i> Add another option
                </button>

                <div id="variantsSummary" class="fw-semibold mb-1"></div>

                <div class="bulk-bar" id="bulkBar" style="display:none">
                    <span>Apply to all{{ $isEdit ? ' (new variants only for stock)' : '' }}:</span>
                    <input type="number" step="0.01" min="0" id="bulkPrice" placeholder="Price">
                    <input type="number" step="1" min="0" id="bulkStock" placeholder="Stock">
                    <button type="button" class="btn-custom btn-custom-light" id="bulkApply">Apply</button>
                    <button type="button" class="link-btn" id="restoreBtn" style="display:none">Restore removed
                        variants</button>
                </div>

                <div class="variants-table-wrap" id="variantsTableWrap"></div>
            </fieldset>
        </div>

        @foreach ($variantErrors as $msg)
            <div class="form-feedback-custom invalid-custom mt-2"><i class="bi bi-exclamation-circle-fill"></i>
                {{ $msg }}</div>
        @endforeach
    </div>

    {{-- VISIBILITY --}}
    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="section-title">Visibility</div>
        <div class="row align-items-end">
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label-custom">Status</label>
                @php $selStatus = old('status', $product->status ?? 'active'); @endphp
                <select name="status" id="status"
                    class="form-select-custom @error('status') is-invalid-custom @enderror">
                    <option value="active" {{ $selStatus === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ $selStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="inactive" {{ $selStatus === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @include('seller.products._error', ['field' => 'status'])
            </div>

            <div class="col-md-6 mb-3">
                <input type="hidden" name="is_featured" value="0">
                <div class="form-switch-custom">
                    <input name="is_featured" value="1" class="form-switch-input-custom" type="checkbox"
                        id="is_featured" {{ $featured ? 'checked' : '' }}>
                    <label class="form-switch-label" for="is_featured">Feature this product</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn-custom btn-custom-primary" type="submit">{{ $isEdit ? 'Save Changes' : 'Create Product' }}</button>
        <a href="{{ route('seller.products.index') }}" class="btn-custom btn-custom-light">Cancel</a>
    </div>
</form>
