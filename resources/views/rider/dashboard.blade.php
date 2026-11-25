@extends('layouts.rider')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}.</p>
        </div>
    </div>

    <div class="card border-light shadow-sm p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                style="width: 56px; height: 56px; background: #e9f4ec;">
                <i class="bi bi-bicycle fs-3 text-success"></i>
            </div>
            <div>
                <h2 class="h5 mb-1">Your rider account is ready</h2>
                <p class="text-muted mb-0">Assigned delivery orders will be available in your Orders section.</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('rider.orders.index') }}" class="btn-custom btn-custom-primary">
                View Orders <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
@endsection
