<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagedUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_sidebar_links_to_seller_and_customer_lists(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.sellers.index'), false)
            ->assertSee(route('admin.customers.index'), false);
    }

    public function test_seller_and_customer_lists_only_show_their_own_role_and_hide_numeric_ids(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'name' => 'Listed Seller',
            'username' => 'listed-seller',
            'image' => 'profiles/listed-seller.jpg',
            'status' => 'Active',
        ]);
        User::factory()->create(['role' => 'customer', 'name' => 'Not a Seller']);
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Listed Customer',
            'username' => 'listed-customer',
            'status' => 'Inactive',
        ]);
        User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer Missing Contact',
            'username' => null,
            'email' => null,
            'phone' => null,
        ]);

        $sellerResponse = $this->get(route('admin.sellers.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $sellerResponse->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', $seller->name)
            ->assertJsonPath('data.0.status', '<span class="badge-table success">Active</span>');
        $this->assertStringContainsString('storage/profiles/listed-seller.jpg', $sellerResponse->json('data.0.image'));
        $this->assertArrayNotHasKey('id', $sellerResponse->json('data.0'));
        $this->assertStringNotContainsString('userKey='.$seller->id, $sellerResponse->getContent());

        $customerResponse = $this->get(route('admin.customers.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $customerResponse->assertJsonPath('recordsTotal', 3)
            ->assertJsonPath('recordsFiltered', 3);
        $this->assertStringContainsString(
            asset('assets/images/no_image.png'),
            $customerResponse->json('data.0.image')
        );
        $missingContactRow = collect($customerResponse->json('data'))
            ->firstWhere('name', 'Customer Missing Contact');
        $this->assertSame('N/A', $missingContactRow['username']);
        $this->assertSame('N/A', $missingContactRow['email']);
        $this->assertSame('N/A', $missingContactRow['phone']);

        $filteredCustomers = $this->get(route('admin.customers.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => 'Inactive',
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $filteredCustomers->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', $customer->name);
    }

    public function test_admin_can_view_and_change_seller_or_customer_status(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'status' => 'Active',
            'image' => 'profiles/admin-view-seller.jpg',
        ]);
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'Inactive']);
        $sellerKey = $this->routeKeyFromList('admin.sellers.index', $seller->name);
        $customerKey = $this->routeKeyFromList('admin.customers.index', $customer->name);

        $this->get(route('admin.sellers.show', ['userKey' => $sellerKey]))
            ->assertOk()
            ->assertSee($seller->name)
            ->assertSee('storage/profiles/admin-view-seller.jpg', false)
            ->assertSee('Active');
        $this->get(route('admin.customers.edit', ['userKey' => $customerKey]))
            ->assertOk()
            ->assertSee('Inactive');

        $this->put(route('admin.sellers.update', ['userKey' => $sellerKey]), ['status' => 'Inactive'])
            ->assertRedirect(route('admin.sellers.index'));
        $this->put(route('admin.customers.update', ['userKey' => $customerKey]), ['status' => 'Active'])
            ->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('users', ['id' => $seller->id, 'status' => 'Inactive']);
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status' => 'Active']);
    }

    public function test_status_updates_reject_invalid_values_and_role_mismatches(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'Active']);
        $customerKey = $this->routeKeyFromList('admin.customers.index', $customer->name);

        $this->put(route('admin.customers.update', ['userKey' => $customerKey]), ['status' => 'admin'])
            ->assertSessionHasErrors('status');
        $this->get(route('admin.sellers.show', ['userKey' => $customerKey]))->assertNotFound();
        $this->get(route('admin.customers.show', ['userKey' => 'not-encrypted']))->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status' => 'Active']);
    }

    private function routeKeyFromList(string $routeName, string $name): string
    {
        $response = $this->get(route($routeName, [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => $name, 'regex' => 'false'],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();

        preg_match('#/admin/(?:sellers|customers)/([^/"]+)/show#', $response->json('data.0.actions'), $matches);

        $this->assertNotEmpty($matches[1] ?? null);

        return $matches[1];
    }
}
