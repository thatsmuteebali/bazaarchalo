<?php

namespace Tests\Unit;

use App\Http\Requests\CheckoutRequest;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CheckoutRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_allows_an_empty_email_and_requires_an_unformatted_mobile_number(): void
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash on Delivery',
            'slug' => 'cod',
            'status' => 'active',
        ]);

        $validator = Validator::make([
            'name' => 'Ahmed Khan',
            'phone' => '03000000000',
            'email' => '',
            'address' => 'House 12, Main Road',
            'payment_method' => $paymentMethod->id,
            'terms' => '1',
        ], (new CheckoutRequest())->rules());

        $this->assertTrue($validator->passes(), $validator->errors()->toJson());

        $validator = Validator::make([
            'name' => 'Ahmed Khan',
            'phone' => '0300-0000000',
            'address' => 'House 12, Main Road',
            'payment_method' => $paymentMethod->id,
            'terms' => '1',
        ], (new CheckoutRequest())->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('phone', $validator->errors()->messages());
    }

    public function test_checkout_only_accepts_active_payment_methods(): void
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Custom Wallet',
            'slug' => 'custom-wallet',
            'status' => 'active',
        ]);
        $data = [
            'name' => 'Ahmed Khan',
            'phone' => '03000000000',
            'address' => 'House 12, Main Road',
            'payment_method' => '',
            'terms' => '1',
        ];

        $validator = Validator::make($data, (new CheckoutRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('payment_method', $validator->errors()->messages());

        $validator = Validator::make(array_merge($data, ['payment_method' => $paymentMethod->id]), (new CheckoutRequest())->rules());
        $this->assertTrue($validator->passes(), $validator->errors()->toJson());

        $paymentMethod->update(['status' => 'inactive']);
        $validator = Validator::make(array_merge($data, ['payment_method' => $paymentMethod->id]), (new CheckoutRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('payment_method', $validator->errors()->messages());
    }
}
