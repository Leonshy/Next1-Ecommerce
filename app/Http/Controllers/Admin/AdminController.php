<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.home');
    }

    public function home()
    {
        $stats = [
            'total_users'       => User::count(),
            'active_products'   => Product::where('is_active', true)->count(),
            'monthly_orders'    => Order::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue'   => Order::whereMonth('created_at', now()->month)
                ->whereIn('status', ['confirmado', 'procesando', 'enviado', 'entregado'])
                ->sum('total'),
        ];

        $recentOrders = Order::latest()->limit(10)->get();

        return view('admin.home', compact('stats', 'recentOrders'));
    }
}
