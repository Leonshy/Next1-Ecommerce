<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasUuids;

    protected $keyType   = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'email', 'status', 'subscribed_at', 'verified_at',
        'verification_token', 'token_expires_at',
    ];

    protected $hidden = ['verification_token'];

    protected $casts = [
        'subscribed_at'  => 'datetime',
        'verified_at'    => 'datetime',
        'token_expires_at' => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Solo suscriptores verificados (la lista exportable). */
    public function scopeVerified($q)
    {
        return $q->where('status', 'verificado');
    }

    /** Pendientes cuyo token aún no expiró (72h de gracia para mostrar en admin). */
    public function scopeActivePending($q)
    {
        return $q->where('status', 'pendiente')
                 ->where('subscribed_at', '>=', now()->subHours(72));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isExpiredPending(): bool
    {
        return $this->status === 'pendiente'
            && $this->subscribed_at->lt(now()->subHours(72));
    }

    public function markVerified(): void
    {
        $this->update([
            'status'             => 'verificado',
            'verified_at'        => now(),
            'verification_token' => null,
            'token_expires_at'   => null,
        ]);
    }

    // ── Lógica de suscripción (punto único) ───────────────────────────────────

    /**
     * Suscribe un email al newsletter.
     * Devuelve el token generado si se debe enviar email de verificación, null si no.
     * Nunca lanza excepción por email duplicado (evita enumeración).
     */
    public static function subscribe(string $email): ?string
    {
        $email = strtolower(trim($email));

        $existing = static::where('email', $email)->first();

        if ($existing) {
            // Ya verificado: no hacer nada (no revelar al llamador)
            if ($existing->status === 'verificado') {
                return null;
            }

            // Pendiente: solo reenviar si el token actual expiró (evitar spam)
            if ($existing->token_expires_at && $existing->token_expires_at->isFuture()) {
                return null; // Token aún válido, no reenviar
            }

            // Token expirado o sin token: generar nuevo y reenviar
            $token = Str::random(64);
            $existing->update([
                'verification_token' => $token,
                'token_expires_at'   => now()->addDays(30),
                'subscribed_at'      => now(), // Reinicia el contador de 72h
            ]);

            return $token;
        }

        // Nuevo suscriptor
        $token = Str::random(64);
        static::create([
            'email'              => $email,
            'status'             => 'pendiente',
            'subscribed_at'      => now(),
            'verification_token' => $token,
            'token_expires_at'   => now()->addDays(30),
        ]);

        return $token;
    }

    /**
     * Verifica un suscriptor por token. Funciona incluso si el token expiró la ventana de 72h.
     * Devuelve true si se verificó, false si el token no existe o ya fue usado.
     */
    public static function verifyByToken(string $token): bool
    {
        $subscriber = static::where('verification_token', $token)->first();

        if (!$subscriber) return false;

        // Token expirado (30 días) → ya no es válido
        if ($subscriber->token_expires_at && $subscriber->token_expires_at->isPast()) {
            return false;
        }

        $subscriber->markVerified();
        return true;
    }
}
