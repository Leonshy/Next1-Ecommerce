<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(string $id)
    {
        $order = Order::with(['items', 'user', 'invoice'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pendiente,confirmado,procesando,enviado,entregado,cancelado',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // Generate invoice when confirmed
        if ($request->status === 'confirmado' && !$order->invoice) {
            Invoice::create([
                'order_id'      => $order->id,
                'user_id'       => $order->user_id,
                'business_name' => $order->customer_name,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'shipping_city'  => $order->shipping_city,
                'subtotal'       => $order->subtotal,
                'discount'       => $order->discount,
                'shipping_cost'  => $order->shipping_cost,
                'total'          => $order->total,
                'items'          => $order->items->map(fn($i) => [
                    'product_name'  => $i->product_name,
                    'product_image' => $i->product_image,
                    'quantity'      => $i->quantity,
                    'unit_price'    => $i->unit_price,
                    'total_price'   => $i->total_price,
                ])->toArray(),
            ]);
        }

        return back()->with('success', 'Estado actualizado.');
    }
}
