@extends('layouts.app')
@section('customCss')
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Profile</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.account') }}">My Account</a></li>
            <li class="breadcrumb-item active text-white">Profile</li>
        </ol>
    </div>
    <main class="container-fluid py-5 account-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5 align-items-start">
                @include('customer.sidebar')
                <section class="col-lg-8 col-xl-9">
                    <div class="account-panel bg-light rounded p-4 p-md-5">
                        <div class="mb-4">
                            <p class="text-secondary fw-bold text-uppercase mb-2">
                                Personal details
                            </p>
                            <h2 class="display-6 mb-2">Profile</h2>
                            <p class="text-muted mb-0">
                                Keep your personal information up to date.
                            </p>
                        </div>
                        <form>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label><input type="text" class="form-control"
                                        value="{{auth()->user()->name}}" />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label><input type="email"
                                        class="form-control" value="{{auth()->user()->email}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone number</label><input type="tel" class="form-control"
                                        value="{{auth()->user()->phone}}" />
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary rounded-pill py-3 px-5 text-white">
                                        Save changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
