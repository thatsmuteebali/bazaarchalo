@extends('layouts.rider')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">View deliveries assigned to you.</p>
        </div>
    </div>

    <div class="card border-light shadow-sm p-5 text-center">
        <div class="mb-3">
            <i class="bi bi-receipt text-muted" style="font-size: 2.5rem;"></i>
        </div>
        <h2 class="h5">No assigned orders yet</h2>
        <p class="text-muted mb-0">Orders assigned to your rider account will appear here.</p>
    </div>
@endsection
