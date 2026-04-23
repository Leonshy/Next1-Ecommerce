<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoparService
{
    private ?string $publicKey;
    private ?string $privateKey;
    private string  $environment;

    private const API_BASE        = 'https://api.pagopar.com/api';
    private const CHECKOUT_URL    = 'https://www.pagopar.com/pagos/';
    private const FORMA_PAGO      = 9; // Bancard (tarjetas)

    public function __construct()
    {
        $setting = PaymentSetting::getProvider('pagopar');

        $this->publicKey   = $setting?->public_key;
        $this->privateKey  = $setting?->private_key;
        $this->environment = $setting?->environment ?? 'sandbox';
    }

    // ── Tokens ────────────────────────────────────────────────────────────────

    private function tokenOrden(string $idPedido, float $monto): string
    {
        return sha1($this->privateKey . $idPedido . strval(floatval($monto)));
    }

    private function tokenConsulta(): string
    {
        return sha1($this->privateKey . 'CONSULTA');
    }

    private function tokenReversar(): string
    {
        return sha1($this->privateKey . 'PEDIDO-REVERSAR');
    }

    public function tokenWebhook(string $hashPedido): string
    {
        return sha1($this->privateKey . $hashPedido);
    }

    // ── Crear orden ───────────────────────────────────────────────────────────

    public function createOrder(Order $order): array
    {
        $order->loadMissing('items');

        $idPedido = $order->order_number;
        $monto    = (float) $order->total;
        $token    = $this->tokenOrden($idPedido, $monto);

        // Construir compras_items
        $items = [];
        foreach ($order->items as $i => $item) {
            $items[] = [
                'id_producto'                    => ($i + 1),
                'nombre'                         => mb_substr($item->product_name, 0, 100),
                'descripcion'                    => mb_substr($item->product_name, 0, 100),
                'cantidad'                       => (int) $item->quantity,
                'precio_total'                   => (float) $item->total_price,
                'categoria'                      => '909',
                'ciudad'                         => '1',
                'public_key'                     => $this->publicKey,
                'url_imagen'                     => $item->product_image ?? '',
                'vendedor_telefono'              => '',
                'vendedor_direccion'             => '',
                'vendedor_direccion_referencia'  => '',
                'vendedor_direccion_coordenadas' => '',
            ];
        }

        // Si hay descuento o envío, ajustamos el último item para que los totales cierren
        $sumItems = array_sum(array_column($items, 'precio_total'));
        $diff     = round($monto - $sumItems, 2);
        if ($diff != 0 && count($items) > 0) {
            $items[count($items) - 1]['precio_total'] += $diff;
        }

        $comprador = [
            'ruc'                  => '',
            'email'                => $order->customer_email,
            'nombre'               => $order->customer_name,
            'telefono'             => $this->sanitizePhone($order->customer_phone ?? ''),
            'documento'            => '00000000',
            'tipo_documento'       => 'CI',
            'ciudad'               => '1',
            'direccion'            => '',
            'coordenadas'          => '',
            'razon_social'         => $order->customer_name,
            'direccion_referencia' => null,
        ];

        $payload = [
            'token'               => $token,
            'public_key'          => $this->publicKey,
            'monto_total'         => $monto,
            'tipo_pedido'         => 'VENTA-COMERCIO',
            'id_pedido_comercio'  => $idPedido,
            'descripcion_resumen' => '',
            'fecha_maxima_pago'   => now()->addHours(48)->format('Y-m-d H:i:s'),
            'forma_pago'          => self::FORMA_PAGO,
            'comprador'           => $comprador,
            'compras_items'       => $items,
        ];

        try {
            $response = Http::timeout(30)
                ->post(self::API_BASE . '/comercios/2.0/iniciar-transaccion', $payload);

            $data = $response->json();

            Log::info('PagoparService::createOrder response', ['data' => $data, 'items_count' => count($items), 'item_fields' => array_keys($items[0] ?? [])]);

            if ($data['respuesta'] ?? false) {
                $hash         = $data['resultado'][0]['data']   ?? null;
                $pagoparOrder = $data['resultado'][0]['pedido'] ?? null;

                return [
                    'success'      => true,
                    'hash'         => $hash,
                    'pagopar_order'=> $pagoparOrder,
                    'redirect_url' => self::CHECKOUT_URL . $hash,
                ];
            }

            $error = $data['resultado'] ?? 'Error desconocido de Pagopar';
            Log::error('PagoparService::createOrder error', ['error' => $error, 'order' => $order->order_number]);

            return ['success' => false, 'message' => $error];

        } catch (\Throwable $e) {
            Log::error('PagoparService::createOrder exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error de conexión con Pagopar.'];
        }
    }

    // ── Consultar estado ──────────────────────────────────────────────────────

    public function queryOrder(string $hashPedido): array
    {
        try {
            $response = Http::timeout(15)
                ->post(self::API_BASE . '/pedidos/1.1/traer', [
                    'hash_pedido'   => $hashPedido,
                    'token'         => $this->tokenConsulta(),
                    'token_publico' => $this->publicKey,
                ]);

            $data = $response->json();

            if ($data['respuesta'] ?? false) {
                $result = $data['resultado'][0] ?? [];
                return [
                    'success'   => true,
                    'pagado'    => (bool) ($result['pagado'] ?? false),
                    'cancelado' => (bool) ($result['cancelado'] ?? false),
                    'monto'     => $result['monto'] ?? null,
                    'forma_pago'=> $result['forma_pago'] ?? null,
                    'raw'       => $result,
                ];
            }

            return ['success' => false, 'message' => $data['resultado'] ?? 'Error'];

        } catch (\Throwable $e) {
            Log::error('PagoparService::queryOrder exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error de conexión.'];
        }
    }

    // ── Reversar pedido ───────────────────────────────────────────────────────

    public function reverseOrder(string $hashPedido): array
    {
        try {
            $response = Http::timeout(15)
                ->post(self::API_BASE . '/pedidos/1.1/reversar', [
                    'hash_pedido'   => $hashPedido,
                    'token'         => $this->tokenReversar(),
                    'token_publico' => $this->publicKey,
                ]);

            $data = $response->json();

            return [
                'success' => (bool) ($data['respuesta'] ?? false),
                'message' => is_string($data['resultado'] ?? null)
                    ? $data['resultado']
                    : 'Reversión procesada.',
                'raw'     => $data,
            ];

        } catch (\Throwable $e) {
            Log::error('PagoparService::reverseOrder exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error de conexión.'];
        }
    }

    // ── Validar webhook ───────────────────────────────────────────────────────

    public function validateWebhook(array $payload): bool
    {
        $resultado  = $payload['resultado'][0] ?? [];
        $hashPedido = $resultado['hash_pedido'] ?? null;
        $tokenRecibido = $resultado['token'] ?? null;

        if (!$hashPedido || !$tokenRecibido) return false;

        return hash_equals($this->tokenWebhook($hashPedido), $tokenRecibido);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isConfigured(): bool
    {
        return !empty($this->publicKey) && !empty($this->privateKey);
    }

    private function sanitizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (empty($phone)) return '+595';
        if (!str_starts_with($phone, '+')) $phone = '+595' . ltrim($phone, '0');
        return $phone;
    }
}
