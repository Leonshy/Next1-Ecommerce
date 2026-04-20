<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SmtpSetting;
use App\Models\SiteContent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SmtpEmailService
{
    // ── Newsletter ────────────────────────────────────────────────────────────

    public function sendNewsletterVerification(string $email, string $token): bool
    {
        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');
        $url       = url('/newsletter/verificar/' . $token);
        $expires   = now()->addDays(30)->format('d/m/Y');

        $html = $this->wrapEmail($storeName, "
            <div style='text-align:center;padding:10px 0 24px;'>
                <span style='font-size:40px;'>📧</span>
                <h2 style='margin:12px 0 6px;font-size:22px;color:#1a537a;'>Confirmá tu suscripción</h2>
                <p style='margin:0;color:#555;font-size:15px;'>Hacé clic en el botón para empezar a recibir nuestras novedades.</p>
            </div>

            <div style='text-align:center;margin:28px 0;'>
                <a href='{$url}'
                   style='display:inline-block;background:#1a537a;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;font-weight:700;'>
                    Confirmar suscripción
                </a>
            </div>

            <p style='font-size:12px;color:#999;text-align:center;margin:0 0 8px;'>
                O copiá este enlace en tu navegador:
            </p>
            <p style='font-size:11px;color:#aaa;text-align:center;word-break:break-all;margin:0 0 24px;'>
                {$url}
            </p>

            <div style='background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;'>
                ⏱ Este enlace es válido hasta el <strong>{$expires}</strong>.<br>
                Si no solicitaste esta suscripción, podés ignorar este email.
            </div>
        ");

        return $this->sendHtmlSilent(
            $email,
            "Confirmá tu suscripción al newsletter de {$storeName}",
            $html
        );
    }

    // ── Transaccionales ───────────────────────────────────────────────────────

    public function sendOrderConfirmation(Order $order): bool
    {
        $order->loadMissing('items');

        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');

        $methodLabel = match ($order->payment_method) {
            'transferencia' => 'Transferencia bancaria',
            'bancard'       => 'Bancard VPOS (tarjeta)',
            default         => ucfirst($order->payment_method ?? 'Online'),
        };

        $statusMsg = $order->payment_method === 'transferencia'
            ? 'Estamos validando tu comprobante de transferencia. Te notificaremos cuando confirmemos el pago.'
            : 'Tu pedido fue recibido y está siendo procesado.';

        $html = $this->wrapEmail($storeName, "
            <h2 style='margin:0 0 8px;font-size:22px;color:#1a537a;'>¡Gracias por tu pedido!</h2>
            <p style='margin:0 0 20px;color:#555;font-size:15px;'>Hola <strong>{$order->customer_name}</strong>, tu pedido fue recibido correctamente.</p>

            <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom:20px;'>
                <tr>
                    <td style='padding:10px 14px;background:#f0f6fb;border-radius:8px 8px 0 0;'>
                        <strong style='font-size:14px;color:#1a537a;'>Pedido {$order->order_number}</strong>
                    </td>
                    <td style='padding:10px 14px;background:#f0f6fb;border-radius:8px 8px 0 0;text-align:right;font-size:13px;color:#777;'>
                        {$order->created_at->format('d/m/Y H:i')}
                    </td>
                </tr>
            </table>

            " . $this->buildItemsTable($order) . "

            <table width='100%' cellpadding='0' cellspacing='0' style='margin:16px 0;'>
                <tr>
                    <td style='padding:5px 0;color:#555;font-size:14px;'>Subtotal</td>
                    <td style='padding:5px 0;text-align:right;font-size:14px;'>Gs. " . number_format($order->subtotal, 0, ',', '.') . "</td>
                </tr>
                <tr>
                    <td style='padding:5px 0;color:#555;font-size:14px;'>Envío</td>
                    <td style='padding:5px 0;text-align:right;font-size:14px;'>" . ($order->shipping_cost > 0 ? 'Gs. ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis') . "</td>
                </tr>
                " . ($order->discount > 0 ? "<tr><td style='padding:5px 0;color:#16a34a;font-size:14px;'>Descuento</td><td style='padding:5px 0;text-align:right;font-size:14px;color:#16a34a;'>-Gs. " . number_format($order->discount, 0, ',', '.') . "</td></tr>" : "") . "
                <tr style='border-top:2px solid #e5e7eb;'>
                    <td style='padding:10px 0 0;font-weight:700;font-size:16px;color:#1a537a;'>Total</td>
                    <td style='padding:10px 0 0;text-align:right;font-weight:700;font-size:16px;color:#1a537a;'>Gs. " . number_format($order->total, 0, ',', '.') . "</td>
                </tr>
            </table>

            <p style='margin:16px 0 6px;font-size:13px;color:#777;'><strong>Método de pago:</strong> {$methodLabel}</p>
            <p style='margin:0 0 20px;font-size:13px;color:#777;'><strong>Dirección de envío:</strong> {$order->shipping_address}, {$order->shipping_city}</p>

            <div style='background:#f0f6fb;border-left:4px solid #1a537a;padding:12px 16px;border-radius:0 8px 8px 0;font-size:13px;color:#444;'>
                {$statusMsg}
            </div>
        ");

        return $this->sendHtmlSilent(
            $order->customer_email,
            "Pedido #{$order->order_number} recibido — {$storeName}",
            $html
        );
    }

    public function sendOrderStatusUpdate(Order $order): bool
    {
        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');

        [$color, $icon, $title, $body] = match ($order->status) {
            'confirmado'  => ['#1d4ed8', '✅', 'Pedido confirmado',     'Tu pago fue confirmado. Estamos preparando tu pedido.'],
            'procesando'  => ['#7c3aed', '⚙️', 'Pedido en preparación', 'Tu pedido está siendo preparado para su despacho.'],
            'enviado'     => ['#0369a1', '🚚', 'Pedido enviado',         'Tu pedido ya está en camino. ¡Pronto llegará!'],
            'entregado'   => ['#15803d', '🎉', 'Pedido entregado',       '¡Tu pedido fue entregado! Esperamos que estés satisfecho.'],
            'cancelado'   => ['#b91c1c', '❌', 'Pedido cancelado',       'Tu pedido fue cancelado. Contactanos si tenés alguna duda.'],
            default       => ['#1a537a', '📦', 'Actualización de pedido', 'El estado de tu pedido fue actualizado.'],
        };

        $html = $this->wrapEmail($storeName, "
            <div style='text-align:center;padding:10px 0 24px;'>
                <span style='font-size:40px;'>{$icon}</span>
                <h2 style='margin:12px 0 6px;font-size:22px;color:{$color};'>{$title}</h2>
                <p style='margin:0;color:#555;font-size:15px;'>Pedido <strong>#{$order->order_number}</strong></p>
            </div>

            <p style='margin:0 0 20px;font-size:15px;color:#444;text-align:center;'>{$body}</p>

            " . $this->buildItemsTable($order) . "

            <table width='100%' cellpadding='0' cellspacing='0' style='margin:16px 0;border-top:2px solid #e5e7eb;'>
                <tr>
                    <td style='padding:12px 0 0;font-weight:700;font-size:16px;color:{$color};'>Total</td>
                    <td style='padding:12px 0 0;text-align:right;font-weight:700;font-size:16px;color:{$color};'>Gs. " . number_format($order->total, 0, ',', '.') . "</td>
                </tr>
            </table>
        ");

        return $this->sendHtmlSilent(
            $order->customer_email,
            "Tu pedido #{$order->order_number} — {$title}",
            $html
        );
    }

    // ── Seguridad ─────────────────────────────────────────────────────────────

    public function sendAccountLocked(string $email, string $name, \Carbon\Carbon $lockedUntil): bool
    {
        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');
        $until     = $lockedUntil->format('H:i \d\e\l d/m/Y');

        $html = $this->wrapEmail($storeName, "
            <div style='text-align:center;padding:10px 0 24px;'>
                <span style='font-size:40px;'>🔒</span>
                <h2 style='margin:12px 0 6px;font-size:22px;color:#b91c1c;'>Cuenta bloqueada temporalmente</h2>
                <p style='margin:0;color:#555;font-size:15px;'>Hola <strong>{$name}</strong>, detectamos múltiples intentos de acceso fallidos en tu cuenta.</p>
            </div>

            <div style='background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#991b1b;'>
                Tu cuenta ha sido bloqueada hasta las <strong>{$until}</strong> por seguridad.
            </div>

            <p style='font-size:14px;color:#444;'>Si fuiste vos quien intentó ingresar y olvidaste tu contraseña, podés restablecerla desde la pantalla de inicio de sesión.</p>
            <p style='font-size:13px;color:#888;margin-top:16px;'>Si no reconocés esta actividad, te recomendamos cambiar tu contraseña en cuanto recuperes el acceso.</p>
        ");

        return $this->sendHtmlSilent($email, "Cuenta bloqueada — {$storeName}", $html);
    }

    public function sendNewLoginAlert(string $email, string $name, string $ip, string $userAgent): bool
    {
        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');
        $when      = now()->format('d/m/Y H:i');
        $browser   = $this->parseUserAgent($userAgent);

        $html = $this->wrapEmail($storeName, "
            <div style='text-align:center;padding:10px 0 24px;'>
                <span style='font-size:40px;'>🛡️</span>
                <h2 style='margin:12px 0 6px;font-size:22px;color:#1a537a;'>Nuevo acceso a tu cuenta</h2>
                <p style='margin:0;color:#555;font-size:15px;'>Hola <strong>{$name}</strong>, se detectó un inicio de sesión desde una ubicación nueva.</p>
            </div>

            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f8fafc;border-radius:8px;padding:16px;margin-bottom:20px;'>
                <tr><td style='padding:5px 0;font-size:13px;color:#555;'><strong>Fecha y hora:</strong></td><td style='padding:5px 0;font-size:13px;color:#333;text-align:right;'>{$when}</td></tr>
                <tr><td style='padding:5px 0;font-size:13px;color:#555;'><strong>Dirección IP:</strong></td><td style='padding:5px 0;font-size:13px;color:#333;text-align:right;'>{$ip}</td></tr>
                <tr><td style='padding:5px 0;font-size:13px;color:#555;'><strong>Dispositivo:</strong></td><td style='padding:5px 0;font-size:13px;color:#333;text-align:right;'>{$browser}</td></tr>
            </table>

            <div style='background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;'>
                Si no fuiste vos, cambiá tu contraseña de inmediato y activá la verificación en dos pasos.
            </div>
        ");

        return $this->sendHtmlSilent($email, "Nuevo acceso detectado — {$storeName}", $html);
    }

    private function parseUserAgent(string $ua): string
    {
        $browser = 'Navegador desconocido';
        if (str_contains($ua, 'Chrome'))  $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari'))  $browser = 'Safari';
        elseif (str_contains($ua, 'Edge'))    $browser = 'Edge';

        $os = 'SO desconocido';
        if (str_contains($ua, 'Windows'))    $os = 'Windows';
        elseif (str_contains($ua, 'Mac'))    $os = 'macOS';
        elseif (str_contains($ua, 'Linux'))  $os = 'Linux';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';

        return "{$browser} en {$os}";
    }

    // ── 2FA ───────────────────────────────────────────────────────────────────

    public function send2FACode(string $email, string $name, string $code): bool
    {
        $storeName = SiteContent::getByKey('store_info')?->metadata['storeName'] ?? config('app.name', 'Next1');

        $html = $this->wrapEmail($storeName, "
            <div style='text-align:center;padding:10px 0 24px;'>
                <span style='font-size:40px;'>🔐</span>
                <h2 style='margin:12px 0 6px;font-size:22px;color:#1a537a;'>Tu código de verificación</h2>
                <p style='margin:0;color:#555;font-size:15px;'>Hola <strong>{$name}</strong>, usá este código para completar tu acceso:</p>
            </div>

            <div style='text-align:center;margin:28px 0;'>
                <div style='display:inline-block;background:#f0f7ff;border:2px dashed #1a537a;border-radius:12px;padding:20px 40px;'>
                    <span style='font-size:40px;font-weight:900;letter-spacing:10px;color:#1a537a;font-family:monospace;'>{$code}</span>
                </div>
            </div>

            <div style='background:#fff8e7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;'>
                ⏱ Este código es válido por <strong>10 minutos</strong>.<br>
                Si no intentaste iniciar sesión, ignorá este mensaje y tu cuenta sigue segura.
            </div>
        ");

        return $this->sendHtmlSilent($email, "Código de verificación — {$storeName}", $html);
    }

    // ── Genérico ──────────────────────────────────────────────────────────────

    public function sendHtml(string $to, string $subject, string $html): bool
    {
        $this->configureMailer();

        Mail::html($html, function (Message $message) use ($to, $subject) {
            $smtp = SmtpSetting::first();
            $from = $smtp?->from_email ?? config('mail.from.address');
            $name = $smtp?->from_name  ?? config('mail.from.name');

            $message->to($to)->subject($subject)->from($from, $name);
        });

        return true;
    }

    public function sendHtmlSilent(string $to, string $subject, string $html): bool
    {
        try {
            return $this->sendHtml($to, $subject, $html);
        } catch (\Throwable $e) {
            Log::error('Email send failed', ['error' => $e->getMessage(), 'to' => $to]);
            return false;
        }
    }

    // ── Privados ──────────────────────────────────────────────────────────────

    private function configureMailer(): void
    {
        $smtp = SmtpSetting::first();

        if (!$smtp || !$smtp->host) {
            throw new \RuntimeException('SMTP_NOT_CONFIGURED');
        }
        if (!$smtp->is_active) {
            throw new \RuntimeException('SMTP_DISABLED');
        }

        $encryption = in_array($smtp->encryption, ['ssl', 'tls']) ? $smtp->encryption : null;

        config([
            'mail.default'                   => 'smtp',
            'mail.mailers.smtp.host'         => $smtp->host,
            'mail.mailers.smtp.port'         => $smtp->port,
            'mail.mailers.smtp.encryption'   => $encryption,
            'mail.mailers.smtp.username'     => $smtp->username,
            'mail.mailers.smtp.password'     => $smtp->password,
            'mail.from.address'              => $smtp->from_email,
            'mail.from.name'                 => $smtp->from_name,
        ]);

        // SSL en puerto 465: habilitar verificación relajada para certificados de hosting compartido
        if ($encryption === 'ssl') {
            config([
                'mail.mailers.smtp.stream' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ],
                ],
            ]);
        }
    }

    private function buildItemsTable(Order $order): string
    {
        $rows = '';
        foreach ($order->items as $item) {
            $rows .= "
            <tr>
                <td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#333;'>{$item->product_name}</td>
                <td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#555;text-align:center;'>x{$item->quantity}</td>
                <td style='padding:9px 12px;border-bottom:1px solid #f0f0f0;font-size:13px;color:#333;text-align:right;font-weight:600;'>Gs. " . number_format($item->total_price, 0, ',', '.') . "</td>
            </tr>";
        }

        return "
        <table width='100%' cellpadding='0' cellspacing='0' style='border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:4px;'>
            <thead>
                <tr style='background:#f8fafc;'>
                    <th style='padding:9px 12px;text-align:left;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Producto</th>
                    <th style='padding:9px 12px;text-align:center;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Cant.</th>
                    <th style='padding:9px 12px;text-align:right;font-size:12px;color:#888;font-weight:600;text-transform:uppercase;'>Total</th>
                </tr>
            </thead>
            <tbody>{$rows}</tbody>
        </table>";
    }

    private function wrapEmail(string $storeName, string $content): string
    {
        return "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'></head>
        <body style='margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f4f4;padding:32px 16px;'>
                <tr><td align='center'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width:580px;'>

                        {{-- Header --}}
                        <tr>
                            <td style='background:#1a537a;padding:24px 32px;border-radius:12px 12px 0 0;text-align:center;'>
                                <span style='color:#fff;font-size:22px;font-weight:900;letter-spacing:1px;'>{$storeName}</span>
                            </td>
                        </tr>

                        {{-- Body --}}
                        <tr>
                            <td style='background:#ffffff;padding:32px;border-radius:0 0 12px 12px;'>
                                {$content}
                            </td>
                        </tr>

                        {{-- Footer --}}
                        <tr>
                            <td style='padding:20px 0;text-align:center;font-size:12px;color:#aaa;'>
                                © " . now()->year . " {$storeName}. Este email fue enviado automáticamente, por favor no respondas.
                            </td>
                        </tr>

                    </table>
                </td></tr>
            </table>
        </body></html>";
    }
}
