@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Addresses</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.account') }}">My Account</a></li>
            <li class="breadcrumb-item active text-white">Addresses</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')
                <section class="col-lg-8 col-xl-9">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <p class="text-secondary fw-bold text-uppercase mb-2">
                                Where we deliver
                            </p>
                            <h2 class="display-6 mb-2">Addresses</h2>
                            <p class="text-muted mb-0">
                                Manage your saved delivery locations.
                            </p>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill py-2 px-4 text-white">
                            <i class="fas fa-plus me-2"></i>Add address
                        </button>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="address-card bg-light rounded p-4 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="mb-0">
                                        Home
                                        <span class="badge bg-secondary text-dark ms-2">Default</span>
                                    </h5>
                                    <button type="button" class="btn btn-sm text-primary" aria-label="Edit home address">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                                <p class="mb-1">Sarah Johnson</p>
                                <p class="text-muted mb-0">
                                    1429 Netus Rd<br />New York, NY 48247<br />United States
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="address-card bg-light rounded p-4 h-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="mb-0">Work</h5>
                                    <button type="button" class="btn btn-sm text-primary" aria-label="Edit work address">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                                <p class="mb-1">Sarah Johnson</p>
                                <p class="text-muted mb-0">
                                    88 Market Street<br />New York, NY 10005<br />United States
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
