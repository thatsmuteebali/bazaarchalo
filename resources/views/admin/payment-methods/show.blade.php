@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $paymentMethod->name }}</h1>
            <p class="page-subtitle">Payment method details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" class="btn-custom btn-custom-primary">Edit Payment Method</a>
            <a href="{{ route('admin.payment-methods.list') }}" class="btn-custom btn-custom-light">Back to Payment Methods</a>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4 mb-4">
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Payment Method Name</dt>
            <dd class="col-sm-9 mb-2">{{ $paymentMethod->name }}</dd>
            <dt class="col-sm-3 mb-2">Slug</dt>
            <dd class="col-sm-9 mb-2">{{ $paymentMethod->slug }}</dd>
            <dt class="col-sm-3 mb-2">Status</dt>
            <dd class="col-sm-9 mb-2">
                <span class="badge-table {{ $paymentMethod->status === 'active' ? 'success' : 'failed' }}">{{ ucfirst($paymentMethod->status) }}</span>
            </dd>
            <dt class="col-sm-3 mb-2">Checkout behavior</dt>
            <dd class="col-sm-9 mb-2">{{ $paymentMethod->slug === 'cod' ? 'Cash on delivery' : 'Online payment' }}</dd>
        </dl>
    </div>
@endsection
