<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingSetting;
use App\Services\BancardService;
use App\Services\PagoparService;
use App\Services\SmtpEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'department' => 'required|string',
            'city'       => 'required|string',
            'subtotal'   => 'required|numeric|min:0',
        ]);

        $settings = ShippingSetting::getDefault();
        $result   = $settings->calculateShipping(
            $request->department,
            $request->city,
            (float) $request->subtotal
        );

        $storePickup = $settings->store_pickup_enabled ? ['enabled' => true, 'cost' => 0, 'label' => 'Retiro en tienda'] : null;

        return response()->json([
            'shipping'    => $result,
            'store_pickup' => $storePickup,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:30',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal     = 0;
            $orderItems   = [];

            foreach ($request->items as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = (float) $product->price;
                $total   = $price * $qty;
                $subtotal += $total;

                $orderItems[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_image' => $product->mainImage?->image_url,
                    'quantity'      => $qty,
                    'unit_price'    => $price,
                    'total_price'   => $total,
                ];
            }

            // Gift card discount
            $discount = 0;
            if ($giftCode = $request->input('gift_card_code')) {
                $giftCard = GiftCard::where('code', $giftCode)->where('status', 'activa')->first();
                if ($giftCard && $giftCard->balance > 0) {
                    $discount = min($giftCard->balance, $subtotal);
                }
            }

            $shippingCost = (float) ($request->input('shipping_cost', 0));
            $total        = max(0, $subtotal - $discount + $shippingCost);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => 'pendiente',
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'shipping_address' => $request->input('shipping_address'),
                'shipping_city'    => $request->input('shipping_city'),
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'notes'            => $request->input('notes'),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            // Process payment
            if ($request->payment_method === 'bancard') {
                return $this->processBancardPayment($order, $request);
            }

            return response()->json(['success' => true, 'order_id' => $order->id, 'order_number' => $order->order_number]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Checkout error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al procesar el pedido.'], 500);
        }
    }

    private function processBancardPayment(Order $order, Request $request): \Illuminate\Http\JsonResponse
    {
        $service = new BancardService();

        $processId = time();
        $order->update(['bancard_process_id' => $processId]);

        $result = $service->createPayment(
            $order,
            route('checkout.confirmation', $order->id),
            route('checkout.index')
        );

        if ($result['success']) {
            return response()->json([
                'success'    => true,
                'payment'    => 'bancard',
                'iframe_url' => $result['iframe_url'],
                'order_id'   => $order->id,
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message'] ?? 'Error en el pago.'], 500);
    }

    public function confirmation(string $orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }

    public function bancardWebhook(Request $request)
    {
        $service = new BancardService();
        $service->handleWebhook($request->all());

        return response()->json(['status' => 'success']);
    }

    // ── Pagopar ───────────────────────────────────────────────────────────────

    public function pagoparWebhook(Request $request)
    {
        $payload  = $request->all();
        $service  = new PagoparService();

        Log::info('Pagopar webhook received', ['payload' => $payload]);

        // Validar token de seguridad
        if (!$service->validateWebhook($payload)) {
            Log::warning('Pagopar webhook token inválido', ['payload' => $payload]);
            return response()->json([['pagado' => false, 'hash_pedido' => '', 'numero_pedido' => '']], 200);
        }

        $resultado  = $payload['resultado'][0] ?? [];
        $hashPedido = $resultado['hash_pedido']              ?? null;
        $pagado     = (bool) ($resultado['pagado']           ?? false);
        $cancelado  = (bool) ($resultado['cancelado']        ?? false);
        $nroPedido  = $resultado['numero_comprobante_interno'] ?? $resultado['numero_pedido'] ?? null;

        $order = Order::where('pagopar_hash', $hashPedido)->first();

        if ($order) {
            $nuevoEstado = $pagado ? 'confirmado' : ($cancelado ? 'cancelado' : $order->status);

            if ($order->status !== $nuevoEstado) {
                $order->update(['status' => $nuevoEstado]);

                if ($pagado) {
                    try {
                        (new SmtpEmailService())->sendOrderConfirmation($order);
                    } catch (\Throwable) {}
                }
            }
        } else {
            Log::warning('Pagopar webhook: orden no encontrada', ['hash' => $hashPedido]);
        }

        // Respuesta requerida por Pagopar
        return response()->json([[
            'pagado'        => $pagado,
            'hash_pedido'   => $hashPedido,
            'numero_pedido' => $nroPedido,
        ]], 200);
    }

    public function pagoparReturn(Request $request)
    {
        // Pagopar redirige con ?hash_pedido=xxx en la URL
        $hash  = $request->query('hash_pedido');
        $order = $hash ? Order::with('items')->where('pagopar_hash', $hash)->first() : null;

        if (!$order) {
            Log::warning('Pagopar return: orden no encontrada', ['hash' => $hash]);
            return redirect()->route('home');
        }

        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        // Consultar estado real en Pagopar si sigue pendiente
        if ($order->status === 'pendiente_pagopar') {
            try {
                $result = (new PagoparService())->queryOrder($order->pagopar_hash);
                if ($result['success']) {
                    if ($result['pagado']) {
                        $order->update(['status' => 'confirmado']);
                        $order->refresh();
                        try {
                            (new SmtpEmailService())->sendOrderConfirmation($order);
                        } catch (\Throwable) {}
                    } elseif ($result['cancelado']) {
                        $order->update(['status' => 'cancelado']);
                        $order->refresh();
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Pagopar pagoparReturn query error', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('checkout.confirmation', $order->id);
    }
}
