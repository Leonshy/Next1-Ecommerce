<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BancardService
{
    private ?string $publicKey;
    private ?string $privateKey;
    private string $environment;
    private string $baseUrl;

    public function __construct()
    {
        $setting = PaymentSetting::getProvider('bancard');

        $this->publicKey   = $setting?->public_key;
        $this->privateKey  = $setting?->private_key;
        $this->environment = $setting?->environment ?? 'sandbox';
        $this->baseUrl     = $this->environment === 'production'
            ? 'https://vpos.infonet.com.py/vpos/api/0.3'
            : 'https://vpos.infonet.com.py:8888/vpos/api/0.3';
    }

    private function generateToken(string ...$parts): string
    {
        return md5($this->privateKey . implode('', $parts));
    }

    public function createPayment(Order $order, string $returnUrl, string $cancelUrl): array
    {
        $shopProcessId = $order->bancard_process_id ?? time();
        $amount        = number_format((float) $order->total, 2, '.', '');
        $currency      = 'PYG';
        $description   = 'Orden ' . $order->order_number;

        $token = $this->generateToken((string) $shopProcessId, $amount, $currency);

        $response = Http::post("{$this->baseUrl}/single_buy", [
            'public_key'      => $this->publicKey,
            'operation'       => [
                'token'            => $token,
                'shop_process_id'  => $shopProcessId,
                'currency'         => $currency,
                'amount'           => $amount,
                'description'      => $description,
                'return_url'       => $returnUrl,
                'cancel_url'       => $cancelUrl,
            ],
        ]);

        if ($response->successful() && isset($response['process_id'])) {
            $iframeUrl = $this->environment === 'production'
                ? "https://vpos.infonet.com.py/payment/card?process_id={$response['process_id']}"
                : "https://vpos.infonet.com.py:8888/payment/card?process_id={$response['process_id']}";

            return [
                'success'     => true,
                'process_id'  => $response['process_id'],
                'iframe_url'  => $iframeUrl,
            ];
        }

        Log::error('Bancard createPayment failed', ['response' => $response->json()]);

        return ['success' => false, 'message' => 'Error al crear el pago con Bancard.'];
    }

    public function confirmPayment(int $shopProcessId): array
    {
        $token = $this->generateToken((string) $shopProcessId, 'get_confirmation');

        $response = Http::post("{$this->baseUrl}/single_buy/confirmations", [
            'public_key' => $this->publicKey,
            'operation'  => [
                'token'           => $token,
                'shop_process_id' => $shopProcessId,
            ],
        ]);

        if ($response->successful()) {
            $data         = $response->json();
            $responseCode = $data['operation']['response_code'] ?? null;
            $status       = $responseCode === '00' ? 'approved' : 'rejected';

            return ['success' => true, 'payment_status' => $status, 'details' => $data];
        }

        return ['success' => false, 'payment_status' => 'pending'];
    }

    public function handleWebhook(array $payload): bool
    {
        $operation     = $payload['operation'] ?? [];
        $shopProcessId = $operation['shop_process_id'] ?? null;
        $response      = $operation['response'] ?? null;
        $responseCode  = $operation['response_code'] ?? null;

        if (!$shopProcessId) return false;

        $order = Order::where('bancard_process_id', $shopProcessId)->first();

        if (!$order) return false;

        $approved = $response === 'S' && $responseCode === '00';
        $order->update(['status' => $approved ? 'confirmado' : 'cancelado']);

        return true;
    }
}
