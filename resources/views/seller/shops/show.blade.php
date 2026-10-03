@extends('layouts.seller')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $shop->name }}</h1>
            <p class="page-subtitle">Shop details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('seller.shops.edit', $shop) }}" class="btn-custom btn-custom-primary">Edit Shop</a>
            <a href="{{ route('seller.shops.index') }}" class="btn-custom btn-custom-light">Back to Shops</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        @if ($shop->banner)
            <div class="d-flex justify-content-center align-items-center w-100 mb-4"
                style="min-height: 200px; background-color: #f8f9fa; overflow: hidden; border-radius: 8px;">

                <img src="{{ asset('storage/' . $shop->banner) }}" alt="{{ $shop->name }}" class="img-fluid"
                    style="max-width: 100%; max-height: 320px; width: auto; height: auto; object-fit: contain;">

            </div>
        @endif
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Status</dt>
            <dd class="col-sm-9 mb-2">
                @php
                    $status_label = match ($shop->status) {
                        'active' => 'success',
                        'inactive' => 'failed',
                        default => 'pending',
                    };
                @endphp
                <span class="badge-table {{ $status_label }}">{{ ucfirst($shop->status) }}</span>
            </dd>
            <dt class="col-sm-3 mb-2">Is Primary</dt>
            <dd class="col-sm-9 mb-2">
                {{ $shop->is_primary ? 'Yes': 'No' }}
            </dd>
            <dt class="col-sm-3 mb-2">Address</dt>
            <dd class="col-sm-9 mb-2">{{ $shop->address ?: 'Not provided' }}</dd>
            <dt class="col-sm-3 mb-2">Description</dt>
            <dd class="col-sm-9 mb-2">{!! nl2br(e($shop->description ?: 'No description provided.')) !!}</dd>
        </dl>
    </div>
@endsection
