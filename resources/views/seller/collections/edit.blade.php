@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Collection</h1>
            <p class="page-subtitle">Update {{ $collection->name }}.</p>
        </div>
        <a href="{{ route('seller.collections.index', ['search' => $search, 'page' => $page]) }}"
            class="btn-custom btn-custom-light">Back to Collections</a>
    </div>

    <form method="POST"
        action="{{ route('seller.collections.update', ['collection' => $collection, 'search' => $search, 'page' => $page]) }}"
        enctype="multipart/form-data" id="collection-form">
        @csrf
        @method('PUT')
        <div class="card border-light shadow-sm p-4">
            <div class="row">
                @include('seller.collections._form', ['collection' => $collection])
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn-custom btn-custom-primary" type="submit">Save Changes</button>
                    <a href="{{ route('seller.collections.index', ['search' => $search, 'page' => $page]) }}"
                        class="btn-custom btn-custom-light">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
