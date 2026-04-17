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

    public function export(Request $request): StreamedResponse
    {
        // Por defecto solo verificados (listos para importar en Mailchimp/Brevo)
        // ?all=1 exporta todos incluyendo pendientes
        $query = NewsletterSubscriber::orderBy('subscribed_at', 'desc');
        if (!$request->boolean('all')) {
            $query->verified();
        }
        $subscribers = $query->get();

        $filename = $request->boolean('all')
            ? 'newsletter-todos-' . now()->format('Ymd') . '.csv'
            : 'newsletter-verificados-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($subscribers, $request) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 para que Excel lo abra correctamente
            fwrite($out, "\xEF\xBB\xBF");
            // Formato compatible con Mailchimp y Brevo
            fputcsv($out, ['Email Address', 'Status', 'Date Subscribed', 'Verified At']);
            foreach ($subscribers as $s) {
                fputcsv($out, [
                    $s->email,
                    $s->status === 'verificado' ? 'subscribed' : 'pending',
                    $s->subscribed_at?->format('Y-m-d H:i:s') ?? '',
                    $s->verified_at?->format('Y-m-d H:i:s')   ?? '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
