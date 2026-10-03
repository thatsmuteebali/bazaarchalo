@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Shop</h1>
            <p class="page-subtitle">Update {{ $shop->name }}.</p>
        </div>
        <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Back to Shops</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <form method="POST" action="{{ route('seller.shops.update', $shop) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card border-light shadow-sm p-4">
                    @include('seller.shops._form', ['shop' => $shop])
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn-custom btn-custom-primary" type="submit">Save Changes</button>
                        <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
