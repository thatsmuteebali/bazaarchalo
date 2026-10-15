<?php

namespace Tests\Unit;

use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CheckoutRequestTest extends TestCase
{
    public function test_checkout_allows_an_empty_email_and_requires_an_unformatted_mobile_number(): void
    {
        $validator = Validator::make([
            'name' => 'Ahmed Khan',
            'phone' => '03000000000',
            'email' => '',
            'address' => 'House 12, Main Road',
            'payment_method' => 'cod',
            'terms' => '1',
        ], (new CheckoutRequest())->rules());

        $this->assertTrue($validator->passes(), $validator->errors()->toJson());

        $validator = Validator::make([
            'name' => 'Ahmed Khan',
            'phone' => '0300-0000000',
            'address' => 'House 12, Main Road',
            'payment_method' => 'cod',
            'terms' => '1',
        ], (new CheckoutRequest())->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('phone', $validator->errors()->messages());
    }

    public function test_online_payment_requires_one_of_the_hardcoded_online_options(): void
    {
        $data = [
            'name' => 'Ahmed Khan',
            'phone' => '03000000000',
            'address' => 'House 12, Main Road',
            'payment_method' => 'online',
            'terms' => '1',
        ];

        $validator = Validator::make($data, (new CheckoutRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('online_payment_method', $validator->errors()->messages());

        $validator = Validator::make($data + ['online_payment_method' => 'jazzcash'], (new CheckoutRequest())->rules());
        $this->assertTrue($validator->passes(), $validator->errors()->toJson());
    }
}
