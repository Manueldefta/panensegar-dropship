<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    private string $serverKey;
    private string $signatureKey;
    private string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key');
        $this->signatureKey = (string) (config('services.midtrans.signature_key') ?: $this->serverKey);
        $this->snapApiUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getOrCreateSnapToken(Order $order): string
    {
        if ($order->snap_token) {
            return $order->snap_token;
        }

        if ($this->serverKey === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diisi di file .env.');
        }

        $order->loadMissing(['items.product', 'user']);

        $itemDetails = $order->items->map(fn ($item) => [
            'id' => (string) $item->product_id,
            'price' => (int) $item->selling_price_snapshot,
            'quantity' => (int) $item->quantity,
            'name' => mb_substr($item->product->name, 0, 50),
        ])->values()->all();

        if ($order->shipping_fee > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_fee,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'item_details' => $itemDetails,
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->post($this->snapApiUrl, $payload);

        if ($response->failed() || !$response->json('token')) {
            throw new RuntimeException('Gagal terhubung ke Midtrans: ' . $response->body());
        }

        $token = $response->json('token');
        $order->update(['snap_token' => $token]);

        return $token;
    }

    public function handleWebhook(array $payload): array
    {
        $orderNumber = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmountRaw = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionId = (string) ($payload['transaction_id'] ?? '');
        $txStatus = (string) ($payload['transaction_status'] ?? '');

        if (!$orderNumber || !$statusCode || !$grossAmountRaw || !$signatureKey || !$transactionId) {
            return ['ok' => false, 'code' => 400, 'message' => 'Payload tidak lengkap'];
        }

        // Lapis 1: Verifikasi tanda tangan SHA-512
        $expectedSignature = hash('sha512', $orderNumber . $statusCode . $grossAmountRaw . $this->signatureKey);
        if (!hash_equals($expectedSignature, $signatureKey)) {
            return ['ok' => false, 'code' => 403, 'message' => 'Signature tidak valid'];
        }

        // Lapis 2-5: Lock, cek nominal, audit trail, dan strict state machine
        return DB::transaction(function () use ($payload, $orderNumber, $grossAmountRaw, $transactionId, $txStatus) {
            $order = Order::where('order_number', $orderNumber)->lockForUpdate()->first();

            if (!$order) {
                return ['ok' => false, 'code' => 404, 'message' => 'Order tidak ditemukan'];
            }

            $paidAmount = (int) round((float) $grossAmountRaw);
            if ($paidAmount !== (int) $order->total_amount) {
                return ['ok' => false, 'code' => 400, 'message' => 'Nominal tidak sesuai'];
            }

            Payment::updateOrCreate(
                ['gateway_transaction_id' => $transactionId],
                [
                    'order_id' => $order->id,
                    'payment_type' => $payload['payment_type'] ?? null,
                    'gross_amount' => $paidAmount,
                    'transaction_status' => $txStatus,
                    'raw_payload' => $payload,
                ]
            );

            $isPaid = $txStatus === 'settlement'
                || ($txStatus === 'capture' && ($payload['fraud_status'] ?? 'accept') === 'accept');
            $isFailed = in_array($txStatus, ['cancel', 'deny', 'expire'], true);

            if ($order->status === 'paid') {
                return ['ok' => true, 'code' => 200, 'message' => 'Sudah lunas sebelumnya (idempotent)'];
            }

            if ($isPaid) {
                if ($order->status === 'pending') {
                    $order->update(['status' => 'paid', 'paid_at' => now()]);
                } else {
                    $order->update(['status' => 'need_refund']);
                }
            } elseif ($isFailed && $order->status === 'pending') {
                $order->update(['status' => $txStatus === 'expire' ? 'expired' : 'cancelled']);
            }

            return ['ok' => true, 'code' => 200, 'message' => 'Webhook diproses'];
        });
    }
}