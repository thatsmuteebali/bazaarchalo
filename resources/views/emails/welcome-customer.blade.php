@extends('emails.layout')

@section('title', 'Welcome to Bazaar Chalo')
@section('preheader', 'Your email is verified and your Bazaar Chalo account is ready.')
@section('eyebrow', 'WELCOME TO BAZAAR CHALO')
@section('heading', "Your account is ready, {$userName}!")

@section('body')
    <p style="margin: 0 0 16px; color: #273444; font-size: 16px; line-height: 1.65;">
        Hello {{ $userName }},
    </p>
    <p style="margin: 0 0 24px; color: #526170; font-size: 15px; line-height: 1.7;">
        Your email address has been verified, and your customer account is ready. Discover fresh picks and enjoy a
        smoother way to shop with Bazaar Chalo.
    </p>

    @include('emails.partials.button', [
        'href' => $shopUrl,
        'label' => 'Start shopping',
    ])

    <p style="margin: 24px 0 0; color: #718096; font-size: 13px; line-height: 1.6;">
        We're glad you're here. If you need help, simply reply to this email.
    </p>
@endsection
