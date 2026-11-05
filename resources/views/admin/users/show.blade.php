@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $user->name }}</h1>
            <p class="page-subtitle">{{ $title }} account details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($editRoute, ['userKey' => $userKey]) }}"
                class="btn-custom btn-custom-primary">Edit Status</a>
            <a href="{{ route($indexRoute) }}" class="btn-custom btn-custom-light">Back to {{ $title }}s</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-center align-items-center w-100 mb-4"
            style="min-height: 120px; background-color: #f8f9fa; overflow: hidden; border-radius: 8px;">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/images/no_image.png') }}"
                alt="{{ $user->name }}" class="rounded-circle"
                style="width: 120px; height: 120px; object-fit: cover;"
                onerror="this.src='{{ asset('assets/images/no_image.png') }}'">
        </div>
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Name</dt>
            <dd class="col-sm-9 mb-2">{{ $user->name ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Username</dt>
            <dd class="col-sm-9 mb-2">{{ $user->username ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Email</dt>
            <dd class="col-sm-9 mb-2">{{ $user->email ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Phone</dt>
            <dd class="col-sm-9 mb-2">{{ $user->phone ?: 'N/A' }}</dd>
            <dt class="col-sm-3 mb-2">Status</dt>
            <dd class="col-sm-9 mb-2">
                <span class="badge-table {{ $user->status === 'Active' ? 'success' : 'failed' }}">
                    {{ $user->status }}
                </span>
            </dd>
            <dt class="col-sm-3 mb-2">Registered</dt>
            <dd class="col-sm-9 mb-2">{{ $user->created_at?->format('M d, Y') ?: 'N/A' }}</dd>
        </dl>
    </div>
@endsection
