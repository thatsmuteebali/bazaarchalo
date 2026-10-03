@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Shop</h1>
            <p class="page-subtitle">Create a shop profile.</p>
        </div>
        <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Back to Shops</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <form method="POST" action="{{ route('seller.shops.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card border-light shadow-sm p-4">
                    @include('seller.shops._form')
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn-custom btn-custom-primary" type="submit">Create Shop</button>
                        <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
