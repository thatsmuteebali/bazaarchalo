<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiderOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
    }

    public function test_rider_order_datatable_shows_payable_and_payment_status_for_assigned_orders_only(): void
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $otherRider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $seller = User::factory()->create(['role' => 'seller']);
        $assignedOrder = $this->createOrder($rider, 'Assigned order', 'unpaid', 125.50);
        $assignedOrder->items()->create([
            'seller_id' => $seller->id,
            'product_name' => 'Assigned delivery',
            'unit_price' => 125.50,
            'quantity' => 1,
            'line_total' => 125.50,
            'status' => OrderItem::STATUS_ACCEPTED,
        ]);
        $privateOrder = $this->createOrder($otherRider, 'Private order', 'paid', 30);
        $privateOrder->items()->create([
            'seller_id' => $seller->id,
            'product_name' => 'Private delivery',
            'unit_price' => 30,
            'quantity' => 1,
            'line_total' => 30,
            'status' => OrderItem::STATUS_DELIVERED,
        ]);

        $this->actingAs($rider)
            ->get(route('rider.orders.index'))
            ->assertOk()
            ->assertSee('id="rider-order-table"', false)
            ->assertSee('Payable amount')
            ->assertSee('Payment status');

        $response = $this->get(route('rider.orders.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'payment_status' => Order::PAYMENT_UNPAID,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        $response->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.payable_total', 'Rs. 125.50')
            ->assertJsonPath('data.0.payment_status', '<span class="badge-table pending">Unpaid</span>');
        $this->assertStringContainsString($assignedOrder->order_number, $response->json('data.0.order'));
        $this->assertStringNotContainsString('Private order', $response->getContent());
    }

    public function test_rider_can_mark_cod_order_paid_only_after_every_deliverable_item_is_delivered(): void
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $otherRider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $seller = User::factory()->create(['role' => 'seller']);
        $order = $this->createOrder($rider, 'Cash order', 'unpaid', 75);
        $order->items()->create([
            'seller_id' => $seller->id,
            'product_name' => 'Delivered item',
            'unit_price' => 25,
            'quantity' => 1,
            'line_total' => 25,
            'status' => OrderItem::STATUS_DELIVERED,
        ]);
        $secondItem = $order->items()->create([
            'seller_id' => $seller->id,
            'product_name' => 'Still in transit',
            'unit_price' => 50,
            'quantity' => 1,
            'line_total' => 50,
            'status' => OrderItem::STATUS_SHIPPED,
        ]);
        $payment = $order->payments()->create([
            'method' => Order::METHOD_COD,
            'amount' => 75,
            'status' => OrderPayment::STATUS_PENDING,
        ]);

        $this->actingAs($rider)
            ->get(route('rider.orders.show', $order))
            ->assertOk()
            ->assertSee('Total payable')
            ->assertSee('Quantity 1')
            ->assertDontSee('Mark as paid');

        $this->from(route('rider.orders.show', $order))
            ->patch(route('rider.orders.payment.paid', $order))
            ->assertRedirect(route('rider.orders.show', $order))
            ->assertSessionHasErrors('payment');
        $this->assertSame(Order::PAYMENT_UNPAID, $order->fresh()->payment_status);

        $secondItem->update(['status' => OrderItem::STATUS_DELIVERED]);
        $this->get(route('rider.orders.show', $order))
            ->assertOk()
            ->assertSee('Mark as paid');

        $this->actingAs($otherRider)
            ->patch(route('rider.orders.payment.paid', $order))
            ->assertNotFound();
        $this->assertSame(Order::PAYMENT_UNPAID, $order->fresh()->payment_status);

        $this->actingAs($rider)
            ->patch(route('rider.orders.payment.paid', $order))
            ->assertRedirect(route('rider.orders.show', $order))
            ->assertSessionHas('success', 'Cash received and order payment marked as paid.');

        $this->assertSame(Order::PAYMENT_PAID, $order->fresh()->payment_status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertSame(OrderPayment::STATUS_PAID, $payment->fresh()->status);
        $this->assertSame(75.0, (float) $payment->fresh()->amount);
        $this->assertNotNull($payment->fresh()->paid_at);
        $this->assertDatabaseHas('order_tracking', [
            'order_id' => $order->id,
            'status' => 'payment_received',
            'created_by' => $rider->id,
            'is_public' => true,
        ]);

        $this->patch(route('rider.orders.payment.paid', $order))
            ->assertSessionHasErrors('payment');
    }

    public function test_rider_cannot_collect_online_payment_or_mark_order_paid_before_delivery(): void
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'Active']);
        $seller = User::factory()->create(['role' => 'seller']);
        $order = $this->createOrder($rider, 'Online order', 'unpaid', 80, Order::METHOD_ONLINE);
        $order->items()->create([
            'seller_id' => $seller->id,
            'product_name' => 'Online item',
            'unit_price' => 80,
            'quantity' => 1,
            'line_total' => 80,
            'status' => OrderItem::STATUS_DELIVERED,
        ]);
        $order->payments()->create([
            'method' => Order::METHOD_ONLINE,
            'amount' => 80,
            'status' => OrderPayment::STATUS_PENDING,
        ]);

        $this->actingAs($rider)
            ->from(route('rider.orders.show', $order))
            ->patch(route('rider.orders.payment.paid', $order))
            ->assertRedirect(route('rider.orders.show', $order))
            ->assertSessionHasErrors('payment');

        $this->assertSame(Order::PAYMENT_UNPAID, $order->fresh()->payment_status);
    }

    private function createOrder(
        User $rider,
        string $customerName,
        string $paymentStatus,
        float $payable,
        string $paymentMethod = Order::METHOD_COD,
    ): Order {
        return Order::create([
            'rider_id' => $rider->id,
            'customer_name' => $customerName,
            'customer_phone' => '03000000000',
            'delivery_address' => '12 Market Road',
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'subtotal' => $payable,
            'shipping_total' => 0,
            'total' => $payable,
            'payable_total' => $payable,
            'status' => Order::STATUS_PROCESSING,
        ]);
    }
}
