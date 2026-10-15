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
            'notes'          => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(['cod', 'online'])],
            'online_payment_method' => [
                'required_if:payment_method,online',
                'prohibited_unless:payment_method,online',
                'nullable',
                Rule::in(['jazzcash', 'easypaisa', 'gopayfast', 'bank_account']),
            ],
            'terms'          => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter 11 digits with no spaces or hyphens, for example 03000000000.',
            'terms.accepted' => 'Please accept the terms and conditions to continue.',
            'payment_method.in' => 'Please choose a payment method.',
            'online_payment_method.required_if' => 'Please choose an online payment method.',
            'online_payment_method.prohibited_unless' => 'Choose an online payment method only when paying online.',
            'online_payment_method.in' => 'Please choose a valid online payment method.',
        ];
    }
}
