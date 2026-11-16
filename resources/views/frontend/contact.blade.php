@extends('layouts.app')
@php
    $contact_content = getWebsiteContent('setting');
@endphp
@section('customCss')
    <style>
        .page-header {
            position: relative !important;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('storage/' . ltrim($contact_content['Contact Banner Background Image'], '/')) }}') !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
    </style>
@endsection
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{$contact_content['Contact Banner Title']}}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Contact Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto" style="max-width: 700px;">
                            <h1 class="text-primary">{{$contact_content['Contact Title']}}</h1>
                            {{-- <p class="mb-4">The contact form is currently inactive. Get a functional and working contact
                                form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little code
                                and you're done. <a href="https://htmlcodex.com/contact-form">Download Now</a>.</p> --}}
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 400px;"
                                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d2664.5731687780244!2d74.34439517564441!3d32.3300756738534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzLCsDE5JzQ4LjMiTiA3NMKwMjAnNDkuMSJF!5e1!3m2!1sen!2s!4v1794830383249!5m2!1sen!2s"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        @if (session('status'))
                            <div class="alert alert-success" role="status">{{ session('status') }}</div>
                        @endif
                        @error('contact')
                            <div class="alert alert-danger" role="alert">{{ $message }}</div>
                        @enderror
                        <form action="{{ route('frontend.contact.send') }}" method="POST">
                            @csrf
                            <label class="form-label" for="contact-name">Your Name</label>
                            <input id="contact-name" name="name" type="text" class="w-100 form-control border-0 py-3 mb-2"
                                value="{{ old('name') }}" autocomplete="name" maxlength="150" required>
                            @error('name')
                                <div class="text-danger mb-3">{{ $message }}</div>
                            @enderror
                            <label class="form-label" for="contact-email">Email Address</label>
                            <input id="contact-email" name="email" type="email" class="w-100 form-control border-0 py-3 mb-2"
                                value="{{ old('email') }}" autocomplete="email" maxlength="254" required>
                            @error('email')
                                <div class="text-danger mb-3">{{ $message }}</div>
                            @enderror
                            <label class="form-label" for="contact-message">Your Message</label>
                            <textarea id="contact-message" name="message" class="w-100 form-control border-0 mb-2" rows="5"
                                maxlength="10000" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mb-3">{{ $message }}</div>
                            @enderror
                            <button class="w-100 btn form-control border-secondary py-3 bg-white text-primary"
                                type="submit">Submit</button>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Address</h4>
                                <p class="mb-2">{{$contact_content['Website Address']}}</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Mail Us</h4>
                                <p class="mb-2">{{$contact_content['Website Email']}}</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded bg-white">
                            <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Telephone</h4>
                                <p class="mb-2">{{$contact_content['Website Phone Number']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection
