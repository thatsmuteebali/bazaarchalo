@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Product Settings</h1>
            <p class="page-subtitle">Review {{ $product->name }} and change its status or featured visibility.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn-custom btn-custom-light">Back to Products</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                @if ($product->images->isNotEmpty())
                    <div class="col-12 mb-3">
                        <label class="form-label-custom">Images</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($product->images as $image)
                                <img src="{{ $image->url }}" alt="{{ $product->name }}"
                                    style="max-width: 180px; max-height: 120px; object-fit: contain;">
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Product Name</label>
                    <input type="text" class="form-control-custom" value="{{ $product->name ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Seller</label>
                    <input type="text" class="form-control-custom" value="{{ $product->seller?->name ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Slug</label>
                    <input type="text" class="form-control-custom" value="{{ $product->slug ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Shop</label>
                    <input type="text" class="form-control-custom" value="{{ $product->shop?->name ?: 'N/A' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Category</label>
                    <input type="text" class="form-control-custom" value="{{ $product->category?->name ?: 'N/A' }}" readonly>
                </div>
                @if ($product->has_variants)
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Variants</label>
                        <input type="text" class="form-control-custom" value="{{ $product->variants()->count() }}" readonly>
                    </div>
                @endif
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Price</label>
                    <input type="text" class="form-control-custom" value="{{ $product->has_variants ? $product->variants()->min('price') .'-' . $product->variants()->max('price')  : $product->price }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Stock</label>
                    <input type="text" class="form-control-custom" value="{{ $product->stock }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label-custom">Status</label>
                    <select name="status" id="status"
                        class="form-select-custom @error('status') is-invalid-custom @enderror" required>
                        @foreach (['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $product->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom d-block">Featured Product</label>
                    <input type="hidden" name="is_featured" value="0">
                    <div class="form-switch-custom">
                        <input name="is_featured" value="1"
                            @checked((bool) old('is_featured', $product->is_featured))
                            class="form-switch-input-custom" type="checkbox" id="is_featured">
                        <label class="form-switch-label" for="is_featured">Show as featured product</label>
                    </div>
                    @error('is_featured')
                        <div class="form-feedback-custom invalid-custom">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn-custom btn-custom-primary" type="submit">Update Product Settings</button>
                <a href="{{ route('admin.products.index') }}" class="btn-custom btn-custom-light">Cancel</a>
            </div>
        </div>
    </form>
@endsection
