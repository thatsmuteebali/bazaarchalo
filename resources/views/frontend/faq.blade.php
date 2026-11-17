@extends('layouts.app')
@section('customCss')
    <style>
        .page-header {
            position: relative !important;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('storage/' . ltrim($faq_page_content['FAQ Banner Background Image'], '/')) }}') !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">
            {{$faq_page_content['FAQ Banner Title']}}
        </h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
            <li class="breadcrumb-item active text-white">FAQs</li>
        </ol>
    </div>

    <main class="container-fluid py-5 faq-page">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px">
                <p class="text-secondary fw-bold text-uppercase mb-2">
                    {{$faq_page_content['FAQ Subtitle']}}
                </p>
                <h2 class="display-6 mb-3">{{$faq_page_content['FAQ Title']}}</h2>
                <p class="mb-0">
                    {{$faq_page_content['FAQ Description']}}
                </p>
            </div>
            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-lg-6">
                    <div class="faq-group bg-light rounded p-4 p-md-5 mb-4">
                        @if ($faqs->isNotEmpty())
                            <div class="accordion" id="shoppingFaq">
                                @foreach ($faqs as $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#faqAnswer{{ $faq->id }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="faqAnswer{{ $faq->id }}">
                                                {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="faqAnswer{{ $faq->id }}"
                                            class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                            aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#shoppingFaq">
                                            <div class="accordion-body">
                                                {!! nl2br(e($faq->answer)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No FAQs available at the moment.</p>
                        @endif
                    </div>

                </div>
            </div>
            <div class="text-center bg-light rounded p-4 p-md-5 mt-5">
                <div class="btn-lg-square rounded-circle bg-secondary mx-auto mb-3">
                    <i class="fas fa-headset fa-2x text-white"></i>
                </div>
                <h3 class="mb-2">Still need help?</h3>
                <p class="mb-4">
                    Our team is happy to help with your order or local shopping
                    experience.
                </p>
                <a href="{{ route('frontend.contact') }}" class="btn btn-primary rounded-pill py-3 px-5 text-white">Contact us <i
                        class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </main>
@endsection

@section('customjs')
@endsection
