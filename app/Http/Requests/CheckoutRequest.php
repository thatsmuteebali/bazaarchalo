<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^03\d{9}$/'],
            'email'          => ['nullable', 'email', 'max:150'],
            'address'        => ['required', 'string', 'min:5', 'max:255'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'payment_method' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id')->where('status', 'active'),
            ],
            'terms'          => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter 11 digits with no spaces or hyphens, for example 03000000000.',
            'terms.accepted' => 'Please accept the terms and conditions to continue.',
            'payment_method.exists' => 'Please choose an available payment method.',
        ];
    }
}
