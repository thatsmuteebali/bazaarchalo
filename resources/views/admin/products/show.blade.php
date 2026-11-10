@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Product Details</h1>
            <p class="page-subtitle">Review {{ $product->name }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn-custom btn-custom-primary">Edit Settings</a>
            <a href="{{ route('admin.products.index') }}" class="btn-custom btn-custom-light">Back to Products</a>
        </div>
    </div>

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
                <label class="form-label-custom">Slug</label>
                <input type="text" class="form-control-custom" value="{{ $product->slug ?: 'N/A' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Seller</label>
                <input type="text" class="form-control-custom" value="{{ $product->seller?->name ?: 'N/A' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Shop</label>
                <input type="text" class="form-control-custom" value="{{ $product->shop?->name ?: 'N/A' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Category</label>
                <input type="text" class="form-control-custom" value="{{ $product->category?->name ?: 'N/A' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Collection</label>
                <input type="text" class="form-control-custom" value="{{ $product->collection?->name ?: 'N/A' }}" readonly>
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
                <label class="form-label-custom">Status</label>
                <input type="text" class="form-control-custom" value="{{ ucfirst($product->status) }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Featured</label>
                <input type="text" class="form-control-custom" value="{{ $product->is_featured ? 'Yes' : 'No' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label-custom">Stock</label>
                <input type="text" class="form-control-custom" value="{{ $product->stock }}" readonly>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label-custom">Description</label>
                <textarea class="form-control-custom" rows="4" readonly>{{ $product->description ?: 'N/A' }}</textarea>
            </div>
        </div>
    </div>
@endsection
