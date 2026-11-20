@extends('emails.layout')

@section('title', 'Verify your email address')
@section('preheader', 'Confirm your email address to finish setting up your Bazaar Chalo account.')
@section('eyebrow', 'ACCOUNT SECURITY')
@section('heading', 'Verify your email address')

@section('body')
    <p style="margin: 0 0 16px; color: #273444; font-size: 16px; line-height: 1.65;">
        Hello {{ $userName }},
    </p>
    <p style="margin: 0 0 24px; color: #526170; font-size: 15px; line-height: 1.7;">
        Thanks for creating a Bazaar Chalo account. Please confirm this email address so we know it belongs to you.
    </p>

    @include('emails.partials.button', [
        'href' => $verificationUrl,
        'label' => 'Verify email address',
    ])

    <p style="margin: 24px 0 8px; color: #718096; font-size: 13px; line-height: 1.6;">
        For your security, this link expires in {{ $expirationMinutes }} minutes.
    </p>
    <p style="margin: 0 0 8px; color: #718096; font-size: 13px; line-height: 1.6;">
        If the button doesn't work, copy and paste this link into your browser:
    </p>
    <p style="margin: 0; font-size: 13px; line-height: 1.6; overflow-wrap: anywhere;">
        <a href="{{ $verificationUrl }}" style="color: #198754; word-break: break-all;">
            {{ $verificationUrl }}
        </a>
    </p>
    <p style="margin: 24px 0 0; color: #718096; font-size: 13px; line-height: 1.6;">
        If you didn't create a Bazaar Chalo account, you can safely ignore this email.
    </p>
@endsection
