<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\SmtpEmailService;
use Illuminate\Http\Request;

class AdminEmailTemplateController extends Controller
{
    // ── Defaults ──────────────────────────────────────────────────────────────

    public static function defaults(): array
    {
        return [
            'newsletter_verification' => [
                'name'        => 'Verificación de Newsletter',
                'description' => 'Se envía cuando alguien se suscribe al newsletter para confirmar su email.',
                'subject'     => 'Confirmá tu suscripción al newsletter de {{store_name}}',
                'variables'   => ['store_name', 'url', 'expires'],
                'body_html'   => <<<'HTML'
<div style="text-align:center;padding:10px 0 24px;">
    <span style="font-size:40px;">📧</span>
    <h2 style="margin:12px 0 6px;font-size:22px;color:#1a537a;">Confirmá tu suscripción</h2>
    <p style="margin:0;color:#555;font-size:15px;">Hacé clic en el botón para empezar a recibir nuestras novedades.</p>
</div>

<div style="text-align:center;margin:28px 0;">
    <a href="{{url}}"
       style="display:inline-block;background:#1a537a;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;font-weight:700;">
        Confirmar suscripción
    </a>
</div>

<p style="font-size:12px;color:#999;text-align:center;margin:0 0 8px;">
    O copiá este enlace en tu navegador:
</p>
<p style="font-size:11px;color:#aaa;text-align:center;word-break:break-all;margin:0 0 24px;">
    {{url}}
</p>

<div style="background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;">
    ⏱ Este enlace es válido hasta el <strong>{{expires}}</strong>.<br>
    Si no solicitaste esta suscripción, podés ignorar este email.
</div>
HTML,
            ],

            'order_confirmation' => [
                'name'        => 'Confirmación de Pedido',
                'description' => 'Se envía al cliente cuando realiza un nuevo pedido.',
                'subject'     => 'Pedido #{{order_number}} recibido — {{store_name}}',
                'variables'   => ['store_name', 'customer_name', 'order_number', 'created_at', 'items_table', 'subtotal', 'shipping_cost', 'discount_row', 'total', 'payment_method', 'shipping_address', 'status_message'],
                'body_html'   => <<<'HTML'
<h2 style="margin:0 0 8px;font-size:22px;color:#1a537a;">¡Gracias por tu pedido!</h2>
<p style="margin:0 0 20px;color:#555;font-size:15px;">Hola <strong>{{customer_name}}</strong>, tu pedido fue recibido correctamente.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
    <tr>
        <td style="padding:10px 14px;background:#f0f6fb;border-radius:8px 8px 0 0;">
            <strong style="font-size:14px;color:#1a537a;">Pedido {{order_number}}</strong>
        </td>
        <td style="padding:10px 14px;background:#f0f6fb;border-radius:8px 8px 0 0;text-align:right;font-size:13px;color:#777;">
            {{created_at}}
        </td>
    </tr>
</table>

{{items_table}}

<table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
    <tr>
        <td style="padding:5px 0;color:#555;font-size:14px;">Subtotal</td>
        <td style="padding:5px 0;text-align:right;font-size:14px;">Gs. {{subtotal}}</td>
    </tr>
    <tr>
        <td style="padding:5px 0;color:#555;font-size:14px;">Envío</td>
        <td style="padding:5px 0;text-align:right;font-size:14px;">{{shipping_cost}}</td>
    </tr>
    {{discount_row}}
    <tr style="border-top:2px solid #e5e7eb;">
        <td style="padding:10px 0 0;font-weight:700;font-size:16px;color:#1a537a;">Total</td>
        <td style="padding:10px 0 0;text-align:right;font-weight:700;font-size:16px;color:#1a537a;">Gs. {{total}}</td>
    </tr>
</table>

<p style="margin:16px 0 6px;font-size:13px;color:#777;"><strong>Método de pago:</strong> {{payment_method}}</p>
<p style="margin:0 0 20px;font-size:13px;color:#777;"><strong>Dirección de envío:</strong> {{shipping_address}}</p>

<div style="background:#f0f6fb;border-left:4px solid #1a537a;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:#444;">
    {{status_message}}
</div>
HTML,
            ],

            'order_status_update' => [
                'name'        => 'Actualización de Estado de Pedido',
                'description' => 'Se envía al cliente cuando un admin cambia el estado de su pedido.',
                'subject'     => 'Tu pedido #{{order_number}} — {{title}}',
                'variables'   => ['store_name', 'order_number', 'icon', 'color', 'title', 'body', 'items_table', 'total'],
                'body_html'   => <<<'HTML'
<div style="text-align:center;padding:10px 0 24px;">
    <span style="font-size:40px;">{{icon}}</span>
    <h2 style="margin:12px 0 6px;font-size:22px;color:{{color}};">{{title}}</h2>
    <p style="margin:0;color:#555;font-size:15px;">Pedido <strong>#{{order_number}}</strong></p>
</div>

<p style="margin:0 0 20px;font-size:15px;color:#444;text-align:center;">{{body}}</p>

{{items_table}}

<table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-top:2px solid #e5e7eb;">
    <tr>
        <td style="padding:12px 0 0;font-weight:700;font-size:16px;color:{{color}};">Total</td>
        <td style="padding:12px 0 0;text-align:right;font-weight:700;font-size:16px;color:{{color}};">Gs. {{total}}</td>
    </tr>
</table>
HTML,
            ],

            'account_locked' => [
                'name'        => 'Cuenta Bloqueada',
                'description' => 'Se envía cuando una cuenta es bloqueada por múltiples intentos de acceso fallidos.',
                'subject'     => 'Cuenta bloqueada — {{store_name}}',
                'variables'   => ['store_name', 'name', 'locked_until'],
                'body_html'   => <<<'HTML'
<div style="text-align:center;padding:10px 0 24px;">
    <span style="font-size:40px;">🔒</span>
    <h2 style="margin:12px 0 6px;font-size:22px;color:#b91c1c;">Cuenta bloqueada temporalmente</h2>
    <p style="margin:0;color:#555;font-size:15px;">Hola <strong>{{name}}</strong>, detectamos múltiples intentos de acceso fallidos en tu cuenta.</p>
</div>

<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#991b1b;">
    Tu cuenta ha sido bloqueada hasta las <strong>{{locked_until}}</strong> por seguridad.
</div>

<p style="font-size:14px;color:#444;">Si fuiste vos quien intentó ingresar y olvidaste tu contraseña, podés restablecerla desde la pantalla de inicio de sesión.</p>
<p style="font-size:13px;color:#888;margin-top:16px;">Si no reconocés esta actividad, te recomendamos cambiar tu contraseña en cuanto recuperes el acceso.</p>
HTML,
            ],

            'new_login_alert' => [
                'name'        => 'Alerta de Nuevo Acceso',
                'description' => 'Se envía cuando se detecta un inicio de sesión desde un dispositivo o ubicación nueva.',
                'subject'     => 'Nuevo acceso detectado — {{store_name}}',
                'variables'   => ['store_name', 'name', 'when', 'ip', 'browser'],
                'body_html'   => <<<'HTML'
<div style="text-align:center;padding:10px 0 24px;">
    <span style="font-size:40px;">🛡️</span>
    <h2 style="margin:12px 0 6px;font-size:22px;color:#1a537a;">Nuevo acceso a tu cuenta</h2>
    <p style="margin:0;color:#555;font-size:15px;">Hola <strong>{{name}}</strong>, se detectó un inicio de sesión desde una ubicación nueva.</p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;padding:16px;margin-bottom:20px;">
    <tr><td style="padding:5px 0;font-size:13px;color:#555;"><strong>Fecha y hora:</strong></td><td style="padding:5px 0;font-size:13px;color:#333;text-align:right;">{{when}}</td></tr>
    <tr><td style="padding:5px 0;font-size:13px;color:#555;"><strong>Dirección IP:</strong></td><td style="padding:5px 0;font-size:13px;color:#333;text-align:right;">{{ip}}</td></tr>
    <tr><td style="padding:5px 0;font-size:13px;color:#555;"><strong>Dispositivo:</strong></td><td style="padding:5px 0;font-size:13px;color:#333;text-align:right;">{{browser}}</td></tr>
</table>

<div style="background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;">
    Si no fuiste vos, cambiá tu contraseña de inmediato y activá la verificación en dos pasos.
</div>
HTML,
            ],

            'two_factor_code' => [
                'name'        => 'Código de Verificación 2FA',
                'description' => 'Se envía el código de verificación en dos pasos al iniciar sesión.',
                'subject'     => 'Código de verificación — {{store_name}}',
                'variables'   => ['store_name', 'name', 'code'],
                'body_html'   => <<<'HTML'
<div style="text-align:center;padding:10px 0 24px;">
    <span style="font-size:40px;">🔐</span>
    <h2 style="margin:12px 0 6px;font-size:22px;color:#1a537a;">Tu código de verificación</h2>
    <p style="margin:0;color:#555;font-size:15px;">Hola <strong>{{name}}</strong>, usá este código para completar tu acceso:</p>
</div>

<div style="text-align:center;margin:28px 0;">
    <div style="display:inline-block;background:#f0f7ff;border:2px dashed #1a537a;border-radius:12px;padding:20px 40px;">
        <span style="font-size:40px;font-weight:900;letter-spacing:10px;color:#1a537a;font-family:monospace;">{{code}}</span>
    </div>
</div>

<div style="background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;">
    ⏱ Este código es válido por <strong>10 minutos</strong>.<br>
    Si no intentaste iniciar sesión, ignorá este mensaje y tu cuenta sigue segura.
</div>
HTML,
            ],
        ];
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index()
    {
        $defaults = self::defaults();

        foreach ($defaults as $key => $data) {
            EmailTemplate::firstOrCreate(
                ['template_key' => $key],
                [
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'subject'     => $data['subject'],
                    'body_html'   => $data['body_html'],
                    'variables'   => $data['variables'],
                    'is_active'   => true,
                ]
            );
        }

        $order = array_keys($defaults);
        $templates = EmailTemplate::all()->sortBy(fn($t) => array_search($t->template_key, $order))->values();

        return view('admin.marketing.email-templates', compact('templates'));
    }

    private static function variableDescriptions(): array
    {
        return [
            'store_name'       => 'Nombre de la tienda',
            'url'              => 'Enlace de acción (confirmar, verificar, etc.)',
            'expires'          => 'Fecha de vencimiento del enlace',
            'customer_name'    => 'Nombre completo del cliente',
            'order_number'     => 'Número de pedido (ej: ORD-2026-001)',
            'created_at'       => 'Fecha y hora del pedido',
            'items_table'      => 'Tabla HTML con los productos del pedido',
            'subtotal'         => 'Subtotal del pedido en Gs.',
            'shipping_cost'    => 'Costo de envío (o "Gratis")',
            'discount_row'     => 'Fila de descuento (vacío si no hay)',
            'total'            => 'Total del pedido en Gs.',
            'payment_method'   => 'Método de pago elegido',
            'shipping_address' => 'Dirección de envío completa',
            'status_message'   => 'Mensaje según el estado/método de pago',
            'icon'             => 'Emoji representando el estado del pedido',
            'color'            => 'Color HEX según el estado (ej: #1d4ed8)',
            'title'            => 'Título del estado (ej: Pedido confirmado)',
            'body'             => 'Descripción del estado del pedido',
            'name'             => 'Nombre del usuario',
            'locked_until'     => 'Hora y fecha hasta la que está bloqueada la cuenta',
            'when'             => 'Fecha y hora del acceso detectado',
            'ip'               => 'Dirección IP desde donde se accedió',
            'browser'          => 'Navegador y sistema operativo detectado',
            'code'             => 'Código numérico de verificación 2FA',
        ];
    }

    public function edit(string $key)
    {
        $template = EmailTemplate::where('template_key', $key)->firstOrFail();
        $allDesc  = self::variableDescriptions();

        $varDescriptions = array_map(fn($v, $d) => ['key' => $v, 'desc' => $d], array_keys($allDesc), array_values($allDesc));

        return view('admin.marketing.email-template-edit', compact('template', 'varDescriptions'));
    }

    public function update(Request $request, string $key)
    {
        $template = EmailTemplate::where('template_key', $key)->firstOrFail();

        $data = $request->validate([
            'subject'   => 'required|string|max:300',
            'body_html' => 'required|string',
        ]);

        $template->update($data);

        return back()->with('success', 'Plantilla guardada correctamente.');
    }

    public function preview(string $key)
    {
        $template = EmailTemplate::where('template_key', $key)->firstOrFail();
        $service  = new SmtpEmailService();

        $sampleVars = $this->sampleVars($key);
        $body = $template->body_html;
        foreach ($sampleVars as $var => $value) {
            $body = str_replace('{{' . $var . '}}', $value, $body);
        }

        $html = $service->buildPreviewHtml('Mi Tienda', $body);

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function reset(string $key)
    {
        $defaults = self::defaults();

        if (!isset($defaults[$key])) {
            return back()->with('error', 'Plantilla no encontrada.');
        }

        EmailTemplate::where('template_key', $key)->update([
            'subject'   => $defaults[$key]['subject'],
            'body_html' => $defaults[$key]['body_html'],
        ]);

        return back()->with('success', 'Plantilla restaurada al contenido original.');
    }

    // ── Sample data for preview ───────────────────────────────────────────────

    private function sampleVars(string $key): array
    {
        $itemsTable = "
        <table width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:4px;'>
            <thead><tr style='background:#f8fafc;'>
                <th style='padding:9px 12px;text-align:left;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Producto</th>
                <th style='padding:9px 12px;text-align:center;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Cant.</th>
                <th style='padding:9px 12px;text-align:right;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Total</th>
            </tr></thead>
            <tbody>
                <tr><td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#333;'>Producto de ejemplo</td>
                    <td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#555;text-align:center;'>x2</td>
                    <td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#333;text-align:right;font-weight:600;'>Gs. 150.000</td></tr>
            </tbody>
        </table>";

        return match ($key) {
            'newsletter_verification' => [
                'store_name' => 'Mi Tienda',
                'url'        => 'https://ejemplo.com/newsletter/verificar/TOKEN123',
                'expires'    => now()->addDays(30)->format('d/m/Y'),
            ],
            'order_confirmation' => [
                'store_name'       => 'Mi Tienda',
                'customer_name'    => 'Juan Pérez',
                'order_number'     => 'ORD-2026-001',
                'created_at'       => now()->format('d/m/Y H:i'),
                'items_table'      => $itemsTable,
                'subtotal'         => '150.000',
                'shipping_cost'    => 'Gratis',
                'discount_row'     => '',
                'total'            => '150.000',
                'payment_method'   => 'Transferencia bancaria',
                'shipping_address' => 'Av. España 1234, Asunción',
                'status_message'   => 'Estamos validando tu comprobante de transferencia.',
            ],
            'order_status_update' => [
                'store_name'   => 'Mi Tienda',
                'order_number' => 'ORD-2026-001',
                'icon'         => '✅',
                'color'        => '#1d4ed8',
                'title'        => 'Pedido confirmado',
                'body'         => 'Tu pago fue confirmado. Estamos preparando tu pedido.',
                'items_table'  => $itemsTable,
                'total'        => '150.000',
            ],
            'account_locked' => [
                'store_name'   => 'Mi Tienda',
                'name'         => 'Juan Pérez',
                'locked_until' => now()->addMinutes(30)->format('H:i \d\e\l d/m/Y'),
            ],
            'new_login_alert' => [
                'store_name' => 'Mi Tienda',
                'name'       => 'Juan Pérez',
                'when'       => now()->format('d/m/Y H:i'),
                'ip'         => '192.168.1.1',
                'browser'    => 'Chrome en Windows',
            ],
            'two_factor_code' => [
                'store_name' => 'Mi Tienda',
                'name'       => 'Juan Pérez',
                'code'       => '482917',
            ],
            default => [],
        };
    }
}
