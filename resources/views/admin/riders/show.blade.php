@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $rider->name }}</h1>
            <p class="page-subtitle">Rider account details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.riders.edit', ['riderKey' => $riderKey]) }}"
                class="btn-custom btn-custom-primary">Edit Rider</a>
            <a href="{{ route('admin.riders.index') }}" class="btn-custom btn-custom-light">Back to Riders</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-center align-items-center w-100 mb-4"
            style="min-height: 120px; background-color: #f8f9fa; overflow: hidden; border-radius: 8px;">
            <img src="{{ $rider->image ? asset('storage/'.$rider->image) : asset('assets/images/no_image.png') }}"
                alt="{{ $rider->name }}" class="rounded-circle"
                style="width: 120px; height: 120px; object-fit: cover;"
                onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
        </div>
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Name</dt>
            <dd class="col-sm-9 mb-2">{{ $rider->name }}</dd>
            <dt class="col-sm-3 mb-2">Username</dt>
            <dd class="col-sm-9 mb-2">{{ $rider->username }}</dd>
            <dt class="col-sm-3 mb-2">Email</dt>
            <dd class="col-sm-9 mb-2">{{ $rider->email }}</dd>
            <dt class="col-sm-3 mb-2">Phone</dt>
            <dd class="col-sm-9 mb-2">{{ $rider->phone }}</dd>
            <dt class="col-sm-3 mb-2">Status</dt>
            <dd class="col-sm-9 mb-2">
                <span class="badge-table {{ $rider->status === 'Active' ? 'success' : 'failed' }}">{{ $rider->status }}</span>
            </dd>
            <dt class="col-sm-3 mb-2">Added</dt>
            <dd class="col-sm-9 mb-2">{{ $rider->created_at?->format('M d, Y') ?: 'N/A' }}</dd>
        </dl>
    </div>
@endsection
