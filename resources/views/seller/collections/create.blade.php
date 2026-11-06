@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Collection</h1>
            <p class="page-subtitle">Create a collection for your shop.</p>
        </div>
        <a href="{{ route('seller.collections.index') }}" class="btn-custom btn-custom-light">Back to Collections</a>
    </div>

        <form method="POST" action="{{ route('seller.collections.store') }}" enctype="multipart/form-data" id="collection-form">
            @csrf
            <div class="card border-light shadow-sm p-4">
                <div class="row">
                    @include('seller.collections._form')
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn-custom btn-custom-primary" type="submit">Create Collection</button>
                        <a href="{{ route('seller.collections.index') }}" class="btn-custom btn-custom-light">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
@endsection
