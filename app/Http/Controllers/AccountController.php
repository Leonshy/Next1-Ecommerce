<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserAddress;
use App\Models\BillingData;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user    = auth()->user()->load('profile');
        $orders  = Order::where('user_id', $user->id)->latest()->limit(5)->get();

        return view('account.index', compact('user', 'orders'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function orderShow(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('items', 'invoice')
            ->firstOrFail();

        return view('account.order-show', compact('order'));
    }

    public function addresses()
    {
        $addresses = UserAddress::where('user_id', auth()->id())->get();
        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'department'     => 'required|string',
            'city'           => 'required|string',
            'neighborhood'   => 'nullable|string',
            'street_address' => 'required|string',
            'cross_street_1' => 'nullable|string',
            'house_number'   => 'nullable|string',
            'reference'      => 'nullable|string',
            'is_default'     => 'boolean',
        ]);

        $data['user_id'] = auth()->id();
        UserAddress::create($data);

        return back()->with('success', 'Dirección guardada.');
    }

    public function deleteAddress(string $id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $address->delete();
        return back()->with('success', 'Dirección eliminada.');
    }

    public function wishlist()
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->with('product.mainImage')->get();
        return view('account.wishlist', compact('wishlist'));
    }

    public function toggleWishlist(string $productId)
    {
        $userId = auth()->id();
        $exists = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['in_wishlist' => false]);
        }

        Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
        return response()->json(['in_wishlist' => true]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'nullable|string|max:30',
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'email'     => auth()->user()->email,
            ]
        );

        return back()->with('success', 'Perfil actualizado.');
    }
}
