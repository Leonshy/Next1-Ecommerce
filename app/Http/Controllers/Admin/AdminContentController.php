<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminContentController extends Controller
{
    // ─── Store Info ───────────────────────────────────────────────────────────

    public function storeInfo()
    {
        $record = SiteContent::getByKey('store_info');
        $data   = $record?->metadata ?? $this->defaultStoreInfo();
        return view('admin.content.store-info', compact('data'));
    }

    public function updateStoreInfo(Request $request)
    {
        $validated = $request->validate([
            'storeName'   => 'required|string|max:100',
            'slogan'      => 'nullable|string|max:200',
            'description' => 'nullable|string|max:500',
            'email'       => 'nullable|email|max:150',
            'phone1'      => 'nullable|string|max:30',
            'phone2'      => 'nullable|string|max:30',
            'whatsapp'    => 'nullable|string|max:30',
            'address'     => 'nullable|string|max:300',
            'mapUrl'      => 'nullable|url|max:1000',
        ]);

        $days = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $schedule = [];
        foreach ($days as $day) {
            $schedule[$day] = [
                'start'  => $request->input("schedule_{$day}_start", '08:00'),
                'end'    => $request->input("schedule_{$day}_end",   '18:00'),
                'closed' => $request->boolean("schedule_{$day}_closed"),
            ];
        }

        $networks = ['facebook','instagram','twitter','youtube','tiktok'];
        $socialNetworks = [];
        foreach ($networks as $net) {
            $socialNetworks[$net] = [
                'url'     => $request->input("social_{$net}_url", ''),
                'enabled' => $request->boolean("social_{$net}_enabled"),
            ];
        }

        $metadata = array_merge($validated, [
            'schedule'       => $schedule,
            'socialNetworks' => $socialNetworks,
        ]);

        SiteContent::updateOrCreate(
            ['key' => 'store_info'],
            [
                'title'      => $validated['storeName'],
                'metadata'   => $metadata,
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Información de la tienda guardada correctamente.');
    }

    // ─── About Us ─────────────────────────────────────────────────────────────

    public function aboutUs()
    {
        $record = SiteContent::getByKey('about_us');
        $data   = $record?->metadata ?? $this->defaultAboutUs();
        return view('admin.content.about-us', compact('data'));
    }

    public function updateAboutUs(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:200',
            'subtitle'       => 'nullable|string|max:300',
            'mission'        => 'nullable|string',
            'vision'         => 'nullable|string',
            'ctaTitle'       => 'nullable|string|max:200',
            'ctaDescription' => 'nullable|string|max:500',
        ]);

        // Valores
        $values = [];
        $icons  = $request->input('value_icon', []);
        $titles = $request->input('value_title', []);
        $descs  = $request->input('value_description', []);
        foreach ($icons as $i => $icon) {
            if (!empty($titles[$i])) {
                $values[] = [
                    'icon'        => $icon,
                    'title'       => $titles[$i],
                    'description' => $descs[$i] ?? '',
                ];
            }
        }

        $metadata = [
            'title'          => $request->input('title'),
            'subtitle'       => $request->input('subtitle'),
            'mission'        => $request->input('mission'),
            'vision'         => $request->input('vision'),
            'ctaTitle'       => $request->input('ctaTitle'),
            'ctaDescription' => $request->input('ctaDescription'),
            'values'         => $values,
        ];

        SiteContent::updateOrCreate(
            ['key' => 'about_us'],
            [
                'title'      => $request->input('title'),
                'metadata'   => $metadata,
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Contenido de Quiénes Somos guardado correctamente.');
    }

    // ─── FAQ ──────────────────────────────────────────────────────────────────

    public function faq()
    {
        $record = SiteContent::getByKey('faq');
        $faqs   = $record?->metadata['faqs'] ?? [];
        return view('admin.content.faq', compact('faqs'));
    }

    public function updateFaq(Request $request)
    {
        $questions = $request->input('question', []);
        $answers   = $request->input('answer', []);

        $faqs = [];
        foreach ($questions as $i => $q) {
            if (!empty(trim($q))) {
                $faqs[] = [
                    'id'       => Str::uuid()->toString(),
                    'question' => $q,
                    'answer'   => $answers[$i] ?? '',
                ];
            }
        }

        SiteContent::updateOrCreate(
            ['key' => 'faq'],
            [
                'title'      => 'Preguntas Frecuentes',
                'metadata'   => ['faqs' => $faqs],
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Preguntas frecuentes guardadas correctamente.');
    }

    // ─── Terms ────────────────────────────────────────────────────────────────

    public function terms()
    {
        $record = SiteContent::getByKey('terms');
        return view('admin.content.terms', ['record' => $record]);
    }

    public function updateTerms(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:200',
            'content' => 'nullable|string',
        ]);

        SiteContent::updateOrCreate(
            ['key' => 'terms'],
            [
                'title'      => $request->input('title'),
                'content'    => $request->input('content'),
                'metadata'   => ['last_updated' => now()->toDateString()],
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Términos y condiciones guardados correctamente.');
    }

    // ─── Privacy ──────────────────────────────────────────────────────────────

    public function privacy()
    {
        $record = SiteContent::getByKey('privacy_policy');
        return view('admin.content.privacy', ['record' => $record]);
    }

    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:200',
            'content' => 'nullable|string',
        ]);

        SiteContent::updateOrCreate(
            ['key' => 'privacy_policy'],
            [
                'title'      => $request->input('title'),
                'content'    => $request->input('content'),
                'metadata'   => ['last_updated' => now()->toDateString()],
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Políticas de privacidad guardadas correctamente.');
    }

    // ─── Defaults ─────────────────────────────────────────────────────────────

    private function defaultStoreInfo(): array
    {
        $days = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $schedule = [];
        foreach ($days as $day) {
            $schedule[$day] = ['start' => '08:00', 'end' => '18:00', 'closed' => in_array($day, ['domingo'])];
        }

        return [
            'storeName'      => 'NEXT1',
            'slogan'         => 'Tu tienda de tecnología y más',
            'description'    => '',
            'email'          => '',
            'phone1'         => '',
            'phone2'         => '',
            'whatsapp'       => '',
            'address'        => '',
            'mapUrl'         => '',
            'schedule'       => $schedule,
            'socialNetworks' => [
                'facebook'  => ['url' => '', 'enabled' => false],
                'instagram' => ['url' => '', 'enabled' => false],
                'twitter'   => ['url' => '', 'enabled' => false],
                'youtube'   => ['url' => '', 'enabled' => false],
                'tiktok'    => ['url' => '', 'enabled' => false],
            ],
        ];
    }

    private function defaultAboutUs(): array
    {
        return [
            'title'          => 'Sobre Nosotros',
            'subtitle'       => '',
            'mission'        => '',
            'vision'         => '',
            'ctaTitle'       => '',
            'ctaDescription' => '',
            'values'         => [],
        ];
    }
}
