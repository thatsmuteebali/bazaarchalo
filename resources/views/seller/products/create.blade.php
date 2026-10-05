@extends('layouts.seller')

@section('customCss')
    @include('seller.products._form_css')
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
            @include('seller.products._form', ['product' => null])
        </div>
    </div>
@endsection

@section('customjs')
    @include('seller.products._form_js', ['product' => null])
@endsection
