@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Shop</h1>
            <p class="page-subtitle">Create a shop profile.</p>
        </div>
        <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Back to Shops</a>
    </div>

    <form method="POST" action="{{ route('seller.shops.store') }}" enctype="multipart/form-data" id="shop-form">
        @csrf
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                @include('seller.shops._form')
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn-custom btn-custom-primary" type="submit">Create Shop</button>
                    <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
