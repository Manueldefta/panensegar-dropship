<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Product;

class SecurityAndAutomationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Test',
            'email' => $email,
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => $role,
            'address' => 'Jl. Pengujian Sistem No. 10',
        ]);
    }

    private function makeOrder(User $owner, string $orderNumber, string $status = 'pending'): Order
    {
        return Order::create([
            'order_number' => $orderNumber,
            'user_id' => $owner->id,
            'delivery_date' => now()->addDay()->toDateString(),
            'shipping_address' => $owner->address,
            'subtotal' => 25000,
            'shipping_fee' => 5000,
            'total_amount' => 30000,
            'status' => $status,
        ]);
    }

    public function test_guest_is_redirected_to_login_on_farmer_dashboard(): void
    {
        $this->get(route('farmer.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_farmer_dashboard(): void
    {
        $customer = $this->makeUser('customer', 'pembeli1@sayur.test');

        $this->actingAs($customer)
            ->get(route('farmer.dashboard'))
            ->assertStatus(403);
    }

    public function test_farmer_can_access_farmer_dashboard(): void
    {
        $farmer = $this->makeUser('farmer', 'petani1@sayur.test');

        $this->actingAs($farmer)
            ->get(route('farmer.dashboard'))
            ->assertStatus(200);
    }

    public function test_registration_blocks_privilege_escalation_to_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Penyerang Test',
            'email' => 'attacker@sayur.test',
            'phone' => '081299999999',
            'address' => 'Jl. Percobaan Privilege Escalation No. 99',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // Disisipkan paksa dari form
        ]);

        $response->assertRedirect(route('catalog.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'attacker@sayur.test',
            'role' => 'customer',
        ]);
    }

    public function test_customer_cannot_view_other_customers_order_anti_idor(): void
    {
        $owner = $this->makeUser('customer', 'pemilik@sayur.test');
        $stranger = $this->makeUser('customer', 'oranglain@sayur.test');
        $order = $this->makeOrder($owner, 'SYR-20261125-IDOR1', 'paid');

        $this->actingAs($stranger)
            ->get(route('orders.show', $order->order_number))
            ->assertStatus(403);
    }

    public function test_expire_command_only_expires_old_pending_orders(): void
    {
        $customer = $this->makeUser('customer', 'pembeli2@sayur.test');

        $oldPending = $this->makeOrder($customer, 'SYR-20261125-OLD01', 'pending');
        $freshPending = $this->makeOrder($customer, 'SYR-20261125-NEW01', 'pending');
        $oldPaid = $this->makeOrder($customer, 'SYR-20261125-PAID1', 'paid');

        // Mundurkan waktu pembuatan menjadi 3 jam lalu
        Order::whereIn('id', [$oldPending->id, $oldPaid->id])
            ->update(['created_at' => now()->subHours(3)]);

        $this->artisan('orders:expire-unpaid', ['--minutes' => 120])
            ->assertSuccessful();

        $this->assertEquals('expired', $oldPending->fresh()->status);
        $this->assertEquals('pending', $freshPending->fresh()->status);
        $this->assertEquals('paid', $oldPaid->fresh()->status);
    }

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'bruteforce@sayur.test',
                'password' => 'salah-terus',
            ])->assertSessionHasErrors();
        }

        $this->post('/login', [
            'email' => 'bruteforce@sayur.test',
            'password' => 'salah-terus',
        ])->assertStatus(429);
    }

    public function test_customer_cannot_exceed_max_active_pending_orders(): void
    {
        $this->seed();

        $customer = User::where('email', 'pembeli@sayur.test')->firstOrFail();
        $product = Product::where('is_available', true)->firstOrFail();

        // Buat 2 pesanan pending aktif (batas maksimal)
        for ($i = 0; $i < 2; $i++) {
            $this->actingAs($customer)->post(route('checkout.store'), [
                'items' => [$product->id => 1],
                'shipping_address' => 'Jl. Merdeka No. 10, Salatiga',
            ])->assertRedirect();
        }

        // Pesanan pending ke-3 wajib ditolak (Anti-Denial of Inventory)
        $this->actingAs($customer)->post(route('checkout.store'), [
            'items' => [$product->id => 1],
            'shipping_address' => 'Jl. Merdeka No. 10, Salatiga',
        ])->assertSessionHasErrors('items');
    }
}