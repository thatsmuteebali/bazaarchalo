<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function __construct(protected string $action = 'login') {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()->timeout(10)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            )->json();
        } catch (\Throwable $e) {
            report($e);
            $fail('Could not verify reCAPTCHA. Please try again.');
            return;
        }

        if (
            empty($response['success']) ||
            ($response['action'] ?? null) !== $this->action ||
            ($response['score'] ?? 0) < config('services.recaptcha.min_score')
        ) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
