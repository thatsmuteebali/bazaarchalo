@extends('layouts.app')
@section('customCss')
    <style>
        .page-header {
            position: relative !important;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('storage/' . ltrim($privacy_page_content['Banner Background Image'], '/')) }}') !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{$privacy_page_content['Banner Title']}}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">Privacy Policy</li>
        </ol>
    </div>

    <main class="privacy-page">
        <section class="container-fluid py-5">
            <div class="container py-3">
                <div class="privacy-simple bg-light rounded p-4 p-lg-5">
                    <p class="text-secondary fw-bold text-uppercase mb-2">
                        {{$privacy_page_content['Subtitle']}}
                    </p>
                    <h2 class="display-6 mb-4">{{$privacy_page_content['Title']}}</h2>
                    <div class="privacy-content">
                        {!! $privacy_page_content['Description'] !!}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('customjs')
@endsection
