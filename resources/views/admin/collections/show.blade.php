@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $collection->name }}</h1>
            <p class="page-subtitle">Collection details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.collections.edit', $collection) }}" class="btn-custom btn-custom-primary">Edit Collection Settings</a>
            <a href="{{ route('admin.collections.index') }}" class="btn-custom btn-custom-light">Back to Collections</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-center align-items-center w-100 mb-4"
            style="min-height: 200px; background-color: #f8f9fa; overflow: hidden; border-radius: 8px;">
            <img src="{{ $collection->image ? asset('storage/' . $collection->image) : asset('assets/images/no_image.png') }}"
                alt="{{ $collection->name }}" class="img-fluid"
                style="max-width: 100%; max-height: 320px; width: auto; height: auto; object-fit: contain;"
                onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
        </div>
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Collection Name</dt>
            <dd class="col-sm-9 mb-2">{{ $collection->name ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Seller</dt>
            <dd class="col-sm-9 mb-2">{{ $collection->seller?->name ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Slug</dt>
            <dd class="col-sm-9 mb-2">{{ $collection->slug ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Description</dt>
            <dd class="col-sm-9 mb-2">{!! nl2br(e($collection->description ?: 'N/A')) !!}</dd>
            <dt class="col-sm-3 mb-2">Status</dt>
            <dd class="col-sm-9 mb-2">
                @php
                    $statusClass = match ($collection->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };
                @endphp
                <span class="badge-table {{ $statusClass }}">{{ ucfirst($collection->status ?: 'N/A') }}</span>
            </dd>
            <dt class="col-sm-3 mb-2">Featured Collection</dt>
            <dd class="col-sm-9 mb-2">{{ $collection->is_featured ? 'Yes' : 'No' }}</dd>
        </dl>
    </div>
@endsection
