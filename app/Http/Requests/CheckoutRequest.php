<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the checkout form. Not used yet - use it in the "Place Order" controller method:
 *   public function placeOrder(CheckoutRequest $request) { $data = $request->validated(); ... }
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // 0300-1234567, 0300 1234567 and +92 300 1234567 all become 03001234567
        $digits = preg_replace('/\D+/', '', (string) $this->input('phone'));

        if (str_starts_with($digits, '92') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        }

        $this->merge(['phone' => $digits]);
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^03\d{9}$/'],
            'email'          => ['required', 'email', 'max:150'],
            'province'       => ['required', Rule::in(config('shop.provinces'))],
            'city'           => ['required', 'string', 'max:80'],
            'address'        => ['required', 'string', 'min:10', 'max:255'],
            'landmark'       => ['nullable', 'string', 'max:150'],
            'postal_code'    => ['nullable', 'digits:5'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(array_keys(config('shop.payment_methods')))],
            'terms'          => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Pakistani mobile number, for example 0300-1234567.',
            'terms.accepted' => 'Please accept the terms and conditions to continue.',
            'payment_method.in' => 'Please choose a payment method.',
        ];
    }
}
