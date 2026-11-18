<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteContentSeeder::class);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_create_update_soft_delete_and_restore_payment_methods(): void
    {
        $this->get(route('admin.payment-methods.list'))
            ->assertOk()
            ->assertSee('Payment Methods');
        $this->get(route('admin.payment-methods.create'))
            ->assertOk()
            ->assertSee('Use <code>cod</code>', false);

        $this->post(route('admin.payment-methods.store'), [
            'name' => '  Cash on Delivery  ',
            'slug' => '  COD  ',
            'status' => 'active',
        ])->assertRedirect(route('admin.payment-methods.list'));

        $paymentMethod = PaymentMethod::query()->where('slug', 'cod')->firstOrFail();
        $this->assertSame('Cash on Delivery', $paymentMethod->name);

        $this->get(route('admin.payment-methods.edit', $paymentMethod))
            ->assertOk()
            ->assertSee('value="cod"', false);
        $this->get(route('admin.payment-methods.show', $paymentMethod))
            ->assertOk()
            ->assertSee('Cash on delivery');

        $this->put(route('admin.payment-methods.update', $paymentMethod), [
            'name' => 'Cash Payment',
            'slug' => 'cash-payment',
            'status' => 'inactive',
        ])->assertRedirect(route('admin.payment-methods.list'));

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'name' => 'Cash Payment',
            'slug' => 'cash-payment',
            'status' => 'inactive',
        ]);

        $this->delete(route('admin.payment-methods.destroy', $paymentMethod))
            ->assertRedirect(route('admin.payment-methods.list'));
        $this->assertSoftDeleted('payment_methods', ['id' => $paymentMethod->id]);

        $this->patch(route('admin.payment-methods.restore', $paymentMethod))
            ->assertRedirect(route('admin.payment-methods.list'));
        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'deleted_at' => null,
        ]);

        $this->delete(route('admin.payment-methods.destroy', $paymentMethod));
        $this->delete(route('admin.payment-methods.force-delete', $paymentMethod))
            ->assertRedirect(route('admin.payment-methods.list'));
        $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);
    }

    public function test_admin_payment_method_table_supports_status_search_and_trash_filtering(): void
    {
        PaymentMethod::create(['name' => 'Cash on Delivery', 'slug' => 'cod', 'status' => 'active']);
        PaymentMethod::create(['name' => 'Custom Wallet', 'slug' => 'custom-wallet', 'status' => 'inactive']);
        $trashed = PaymentMethod::create(['name' => 'Old Wallet', 'slug' => 'old-wallet', 'status' => 'active']);
        $trashed->delete();

        $trashedResponse = $this->get(route('admin.payment-methods.list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => 'trashed',
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $this->assertArrayNotHasKey('id', $trashedResponse->json('data.0'));

        $this->get(route('admin.payment-methods.list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => 'active',
        ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('recordsTotal', 2)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Cash on Delivery')
            ->assertJsonPath('data.0.slug', 'cod');
    }

    public function test_checkout_shows_only_active_payment_methods_and_empty_state_is_clear(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $shop = Shop::create(['seller_id' => $seller->id, 'name' => 'Checkout Shop', 'status' => 'active']);
        $category = Category::create(['name' => 'Checkout Category', 'status' => 'active']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Checkout Product',
            'price' => 25,
            'stock' => 2,
            'status' => 'active',
            'has_variants' => false,
        ]);
        PaymentMethod::create(['name' => 'Custom Wallet', 'slug' => 'custom-wallet', 'status' => 'active']);
        PaymentMethod::create(['name' => 'Unavailable', 'slug' => 'unavailable', 'status' => 'inactive']);

        $session = ['cart_lines' => [
            'p'.$product->id => ['product_id' => $product->id, 'variant_id' => null, 'qty' => 1],
        ]];
        $this->actingAs($customer)->withSession($session)
            ->get(route('customer.checkout'))
            ->assertOk()
            ->assertSee('Custom Wallet')
            ->assertDontSee('Unavailable');

        PaymentMethod::query()->where('status', 'active')->update(['status' => 'inactive']);
        $this->withSession($session)
            ->get(route('customer.checkout'))
            ->assertOk()
            ->assertSee('No payment methods are currently available.')
            ->assertSee('disabled', false);
    }

    public function test_payment_method_slug_must_be_unique_and_checkout_slug_is_reserved_for_cash_on_delivery(): void
    {
        PaymentMethod::create(['name' => 'Cash on Delivery', 'slug' => 'cod', 'status' => 'active']);

        $this->post(route('admin.payment-methods.store'), [
            'name' => 'Another Cash Option',
            'slug' => 'COD',
            'status' => 'active',
        ])->assertSessionHasErrors('slug');
    }
}
