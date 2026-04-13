<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGiftCardController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->input('q');
        $status = $request->input('status');

        $giftCards = GiftCard::query()
            ->when($q, fn($query) => $query->where('code', 'like', "%{$q}%")
                ->orWhere('buyer_email', 'like', "%{$q}%")
                ->orWhere('recipient_email', 'like', "%{$q}%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => GiftCard::count(),
            'activa'   => GiftCard::where('status', 'activa')->count(),
            'usada'    => GiftCard::where('status', 'usada')->count(),
            'expirada' => GiftCard::where('status', 'expirada')->count(),
        ];

        return view('admin.marketing.gift-cards', compact('giftCards', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount'          => 'required|numeric|min:1000',
            'buyer_name'      => 'nullable|string|max:100',
            'buyer_email'     => 'nullable|email|max:150',
            'buyer_phone'     => 'nullable|string|max:30',
            'recipient_name'  => 'nullable|string|max:100',
            'recipient_email' => 'nullable|email|max:150',
            'message'         => 'nullable|string|max:500',
            'expires_at'      => 'nullable|date|after:today',
        ]);

        $data['code']         = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        $data['balance']      = $data['amount'];
        $data['status']       = 'activa';
        $data['purchased_at'] = now();

        GiftCard::create($data);
        return back()->with('success', 'Gift card creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $giftCard = GiftCard::findOrFail($id);

        $data = $request->validate([
            'status'          => 'required|in:pendiente,activa,usada,expirada,cancelada',
            'buyer_name'      => 'nullable|string|max:100',
            'buyer_email'     => 'nullable|email|max:150',
            'buyer_phone'     => 'nullable|string|max:30',
            'recipient_name'  => 'nullable|string|max:100',
            'recipient_email' => 'nullable|email|max:150',
            'message'         => 'nullable|string|max:500',
            'expires_at'      => 'nullable|date',
        ]);

        $giftCard->update($data);
        return back()->with('success', 'Gift card actualizada.');
    }

    public function destroy(string $id)
    {
        GiftCard::findOrFail($id)->delete();
        return back()->with('success', 'Gift card eliminada.');
    }
}
