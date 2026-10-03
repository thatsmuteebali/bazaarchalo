@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $collection->name }}</h1>
            <p class="page-subtitle">Collection details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('seller.collections.edit', ['collection' => $collection, 'search' => $search, 'page' => $page]) }}"
                class="btn-custom btn-custom-primary">Edit Collection</a>
            <a href="{{ route('seller.collections.index', ['search' => $search, 'page' => $page]) }}"
                class="btn-custom btn-custom-light">Back to Collections</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        @if ($collection->image)
            <div class="d-flex justify-content-center align-items-center w-100 mb-4"
                style="min-height: 200px; background-color: #f8f9fa; overflow: hidden; border-radius: 8px;">

                <img src="{{ asset('storage/' . $collection->image) }}" alt="{{ $collection->name }}" class="img-fluid"
                    style="max-width: 100%; max-height: 320px; width: auto; height: auto; object-fit: contain;">

            </div>
        @endif
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Status</dt>
            @php
                $status_label = match ($collection->status) {
                    'active' => 'success',
                    'inactive' => 'failed',
                    default => 'pending',
                };
            @endphp
            <dd class="col-sm-9 mb-2">
                <span class="badge-table {{ $status_label }}">{{ ucfirst($collection->status) }}</span>
            </dd>
            <dt class="col-sm-3 mb-2">Description</dt>
            <dd class="col-sm-9 mb-2">{!! nl2br(e($collection->description ?: 'No description provided.')) !!}</dd>
        </dl>
    </div>
@endsection
