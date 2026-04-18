<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSetting;
use App\Models\HcaptchaSetting;
use App\Models\PaymentSetting;
use App\Models\SeoSetting;
use App\Models\ShippingSetting;
use App\Models\SmtpSetting;
use App\Services\SmtpEmailService;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    // ── Shipping ──────────────────────────────────────────────────────────────
    public function shipping()
    {
        $settings = ShippingSetting::getDefault();
        return view('admin.settings.shipping', compact('settings'));
    }

    public function updateShipping(Request $request)
    {
        $settings = ShippingSetting::getDefault();
        $settings->update($request->only([
            'free_shipping_enabled', 'free_shipping_min_amount',
            'store_pickup_enabled', 'envio_propio_enabled', 'zones',
            'aex_enabled', 'aex_api_user', 'aex_api_password',
            'aex_environment',
        ]));

        return back()->with('success', 'Configuración de envíos actualizada.');
    }

    // ── Payments ──────────────────────────────────────────────────────────────
    public function payments()
    {
        $providers = ['bancard', 'pagopar', 'coinbase', 'coinspaid'];
        $settings  = [];
        foreach ($providers as $provider) {
            $settings[$provider] = PaymentSetting::firstOrCreate(
                ['provider' => $provider],
                ['environment' => 'sandbox']
            );
        }
        $transferSettings = \App\Models\SiteContent::getByKey('transfer_settings')?->metadata ?? [
            'bank' => '', 'account_name' => '', 'account_number' => '', 'ruc' => '', 'extra' => '',
        ];
        return view('admin.settings.payments', compact('settings', 'transferSettings'));
    }

    public function updatePayment(Request $request, string $provider)
    {
        if ($provider === 'transferencia') {
            \App\Models\SiteContent::updateOrCreate(
                ['key' => 'transfer_settings'],
                ['metadata' => $request->only(['bank', 'account_name', 'account_number', 'ruc', 'extra'])]
            );
            return back()->with('success', 'Datos de transferencia actualizados.');
        }

        $setting = PaymentSetting::firstOrCreate(['provider' => $provider]);
        $setting->update($request->only(['public_key', 'private_key', 'webhook_secret', 'environment', 'is_enabled']));
        return back()->with('success', 'Configuración de ' . $provider . ' actualizada.');
    }

    public function validatePayment(Request $request, string $provider)
    {
        $setting = PaymentSetting::where('provider', $provider)->first();
        if (!$setting) return back()->with('error', 'Proveedor no encontrado.');

        // Simple format validation
        $valid = match($provider) {
            'bancard'   => strlen($setting->public_key ?? '') >= 20 && strlen($setting->private_key ?? '') >= 20,
            'pagopar'   => strlen($setting->public_key ?? '') >= 10,
            'coinspaid' => strlen($setting->public_key ?? '') >= 10,
            'coinbase'  => $this->validateCoinbase($setting->public_key ?? ''),
            default     => false,
        };

        $setting->update(['is_validated' => $valid, 'is_enabled' => $valid]);
        $msg = $valid ? 'Credenciales válidas.' : 'Credenciales inválidas.';

        return back()->with($valid ? 'success' : 'error', $msg);
    }

    private function validateCoinbase(string $apiKey): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-CC-Api-Key' => $apiKey,
                'X-CC-Version' => '2018-03-22',
            ])->get('https://api.commerce.coinbase.com/charges');
            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    // ── SEO ───────────────────────────────────────────────────────────────────
    public function seo()
    {
        $pages    = ['home', 'products', 'about_us', 'faq', 'global'];
        $settings = [];
        foreach ($pages as $page) {
            $settings[$page] = SeoSetting::firstOrCreate(['page_key' => $page]);
        }
        return view('admin.settings.seo', compact('settings'));
    }

    public function updateSeo(Request $request)
    {
        foreach ($request->input('pages', []) as $pageKey => $data) {
            SeoSetting::updateOrCreate(
                ['page_key' => $pageKey],
                array_filter($data, fn($v) => $v !== null)
            );
        }
        return back()->with('success', 'SEO actualizado.');
    }

    // ── Analytics ─────────────────────────────────────────────────────────────
    public function analytics()
    {
        $settings = AnalyticsSetting::first() ?? new AnalyticsSetting();
        return view('admin.settings.analytics', compact('settings'));
    }

    public function updateAnalytics(Request $request)
    {
        AnalyticsSetting::updateOrCreate([], $request->only([
            'ga4_enabled', 'ga4_measurement_id',
            'meta_pixel_enabled', 'meta_pixel_id',
            'gtm_enabled', 'gtm_container_id',
            'track_view_item', 'track_add_to_cart',
            'track_begin_checkout', 'track_purchase',
        ]));
        return back()->with('success', 'Analytics actualizado.');
    }

    // ── Email / SMTP ──────────────────────────────────────────────────────────
    public function email()
    {
        $settings = SmtpSetting::first() ?? new SmtpSetting();
        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $data = $request->only([
            'host', 'port', 'username', 'from_email', 'from_name', 'is_active',
        ]);

        // Normalizar encryption al enum de la DB
        $enc = $request->input('encryption', 'none');
        $data['encryption'] = in_array($enc, ['ssl', 'tls']) ? $enc : 'none';

        // Solo actualizar contraseña si se envió una nueva
        $password = $request->input('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $data['is_active'] = $request->boolean('is_active');

        SmtpSetting::updateOrCreate([], $data);
        return back()->with('success', 'SMTP actualizado.');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate(['to' => 'required|email']);

        try {
            $service = new SmtpEmailService();
            $service->sendHtml(
                $request->to,
                'Email de prueba - Next1',
                '<h1>¡Funciona!</h1><p>Este es un email de prueba desde el panel de administración de Next1.</p>'
            );
            return back()->with('success', 'Email enviado correctamente a ' . $request->to);
        } catch (\Throwable $e) {
            return back()->with('error', $this->friendlySmtpError($e->getMessage()));
        }
    }

    private function friendlySmtpError(string $error): string
    {
        if (str_contains($error, 'SMTP_NOT_CONFIGURED')) {
            return 'No hay configuración SMTP guardada. Completá los datos y guardá antes de enviar.';
        }
        if (str_contains($error, 'SMTP_DISABLED')) {
            return 'El SMTP está desactivado. Activalo con el toggle y guardá la configuración.';
        }
        if (str_contains($error, '535') || str_contains($error, 'authentication failed')) {
            return 'Contraseña incorrecta. Verificá las credenciales del correo en el servidor.';
        }
        if (str_contains($error, 'Connection refused') || str_contains($error, 'Unable to connect')) {
            return 'No se pudo conectar al servidor de correo. Verificá que el host y el puerto sean correctos.';
        }
        if (str_contains($error, 'getaddrinfo') || str_contains($error, 'name resolution')) {
            return 'El host SMTP no existe o no se puede resolver. Verificá el nombre del servidor.';
        }
        if (str_contains($error, 'ssl') || str_contains($error, 'SSL') || str_contains($error, 'certificate')) {
            return 'Error de certificado SSL. Probá cambiar el cifrado a TLS (puerto 587).';
        }
        if (str_contains($error, 'timed out') || str_contains($error, 'timeout')) {
            return 'El servidor de correo tardó demasiado en responder. Verificá el host y el puerto.';
        }

        return 'Error al enviar el email. Verificá la configuración SMTP.';
    }

    // ── hCaptcha ──────────────────────────────────────────────────────────────
    public function hcaptcha()
    {
        $settings = HcaptchaSetting::first() ?? new HcaptchaSetting();
        return view('admin.settings.hcaptcha', compact('settings'));
    }

    public function updateHcaptcha(Request $request)
    {
        HcaptchaSetting::updateOrCreate([], $request->only([
            'is_enabled', 'site_key', 'secret_key',
            'protect_login', 'protect_register', 'protect_newsletter',
        ]));
        return back()->with('success', 'hCaptcha actualizado.');
    }

    // ── Mantenimiento ─────────────────────────────────────────────────────────
    public function maintenance()
    {
        $record = \App\Models\SiteContent::getByKey('maintenance');
        $data   = $record?->metadata ?? ['is_active' => false, 'message' => '', 'estimated_time' => ''];
        return view('admin.settings.maintenance', compact('data'));
    }

    public function updateMaintenance(Request $request)
    {
        $request->validate([
            'message'        => 'nullable|string|max:300',
            'estimated_time' => 'nullable|string|max:100',
        ]);

        $metadata = [
            'is_active'      => $request->boolean('is_active'),
            'message'        => $request->input('message', 'Estamos realizando tareas de mantenimiento. Volvemos pronto.'),
            'estimated_time' => $request->input('estimated_time', ''),
        ];

        \App\Models\SiteContent::updateOrCreate(
            ['key' => 'maintenance'],
            ['title' => 'Modo Mantenimiento', 'metadata' => $metadata, 'updated_by' => auth()->id()]
        );

        \Illuminate\Support\Facades\Cache::forget('maintenance_mode');

        $status = $metadata['is_active'] ? 'activado' : 'desactivado';
        return back()->with('success', "Modo mantenimiento $status correctamente.");
    }
}
