<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
    }

    public function test_account_shows_dynamic_counts_and_only_the_five_most_recent_customer_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $orderNumbers = [];

        foreach (range(1, 6) as $daysAgo) {
            $order = Order::create([
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => '03000000000',
                'delivery_address' => '12 Market Road',
                'payment_method' => Order::METHOD_COD,
                'subtotal' => 100,
                'total' => 100,
                'payable_total' => 100,
                'status' => Order::STATUS_PENDING,
            ]);
            $order->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
            $orderNumbers[] = $order->order_number;
        }

        $response = $this->actingAs($customer)->get(route('customer.account'));

        $response->assertOk()
            ->assertSee('Orders placed')
            ->assertSee('Wishlist items')
            ->assertSee('>6</strong>', false)
            ->assertDontSee('Reward points')
            ->assertDontSee('account-tabContent');

        foreach (array_slice($orderNumbers, 0, 5) as $orderNumber) {
            $response->assertSee($orderNumber);
        }

        $response->assertDontSee($orderNumbers[5]);
    }
}
