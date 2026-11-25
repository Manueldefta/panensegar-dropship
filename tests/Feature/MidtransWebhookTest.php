<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private string $secretKey = 'test-midtrans-signature-secret';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.midtrans.signature_key', $this->secretKey);
    }

    private function createSampleOrder(string $status = 'pending', int $totalAmount = 51500): Order
    {
        $user = User::create([
            'name' => 'Pembeli Test',
            'email' => 'test@sayur.test',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'address' => 'Jl. Pengujian No. 1',
        ]);

        return Order::create([
            'order_number' => 'SYR-20261125-TEST1',
            'user_id' => $user->id,
            'delivery_date' => now()->addDay()->toDateString(),
            'shipping_address' => 'Jl. Pengujian No. 1',
            'subtotal' => $totalAmount,
            'shipping_fee' => 0,
            'total_amount' => $totalAmount,
            'status' => $status,
        ]);
    }

    private function makePayload(Order $order, array $overrides = []): array
    {
        $statusCode = $overrides['status_code'] ?? '200';
        $grossAmount = $overrides['gross_amount'] ?? number_format($order->total_amount, 2, '.', '');
        $signature = $overrides['signature_key']
            ?? hash('sha512', $order->order_number . $statusCode . $grossAmount . $this->secretKey);

        return array_merge([
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_id' => 'midtrans-tx-001',
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
        ], $overrides);
    }

    public function test_rejects_webhook_with_invalid_signature(): void
    {
        $order = $this->createSampleOrder();
        $payload = $this->makePayload($order, ['signature_key' => 'tanda-tangan-palsu']);

        $response = $this->postJson(route('webhooks.midtrans'), $payload);

        $response->assertStatus(403);
        $this->assertEquals('pending', $order->fresh()->status);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_rejects_webhook_with_tampered_amount(): void
    {
        $order = $this->createSampleOrder(totalAmount: 51500);
        // Nominal diubah jadi Rp100 meski signature-nya valid
        $payload = $this->makePayload($order, ['gross_amount' => '100.00']);

        $response = $this->postJson(route('webhooks.midtrans'), $payload);

        $response->assertStatus(400);
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_marks_order_as_paid_on_valid_settlement_webhook(): void
    {
        $order = $this->createSampleOrder();
        $payload = $this->makePayload($order);

        $response = $this->postJson(route('webhooks.midtrans'), $payload);

        $response->assertStatus(200);
        $order->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway_transaction_id' => 'midtrans-tx-001',
            'transaction_status' => 'settlement',
            'gross_amount' => 51500,
        ]);
    }

    public function test_handles_duplicate_webhook_idempotently(): void
    {
        $order = $this->createSampleOrder();
        $payload = $this->makePayload($order);

        // Midtrans mengirim notifikasi yang sama 2 kali
        $this->postJson(route('webhooks.midtrans'), $payload)->assertStatus(200);
        $this->postJson(route('webhooks.midtrans'), $payload)
            ->assertStatus(200)
            ->assertJson(['message' => 'Sudah lunas sebelumnya (idempotent)']);

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_flags_late_payment_on_expired_order_as_need_refund(): void
    {
        $order = $this->createSampleOrder(status: 'expired');
        $payload = $this->makePayload($order);

        $response = $this->postJson(route('webhooks.midtrans'), $payload);

        $response->assertStatus(200);
        $this->assertEquals('need_refund', $order->fresh()->status);
    }
}