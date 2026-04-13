<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminNewsletterController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');

        $subscribers = NewsletterSubscriber::query()
            ->when($q, fn($query) => $query->where('email', 'like', "%{$q}%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('subscribed_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $stats = [
            'total'      => NewsletterSubscriber::count(),
            'verificado' => NewsletterSubscriber::where('status', 'verificado')->count(),
            'pendiente'  => NewsletterSubscriber::where('status', 'pendiente')->count(),
        ];

        return view('admin.marketing.newsletter', compact('subscribers', 'stats'));
    }

    public function destroy(string $id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        return back()->with('success', 'Suscriptor eliminado.');
    }

    public function export(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::orderBy('subscribed_at', 'desc')->get();

        return response()->streamDownload(function () use ($subscribers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Estado', 'Fecha de suscripción', 'Verificado en']);
            foreach ($subscribers as $s) {
                fputcsv($out, [
                    $s->email,
                    $s->status,
                    $s->subscribed_at?->format('d/m/Y H:i'),
                    $s->verified_at?->format('d/m/Y H:i') ?? '',
                ]);
            }
            fclose($out);
        }, 'newsletter-suscriptores-' . now()->format('Ymd') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
