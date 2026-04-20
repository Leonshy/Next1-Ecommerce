<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorCode;
use App\Models\User;
use App\Services\SmtpEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    // ── Challenge (pantalla de ingreso de código) ─────────────────────────────

    public function show(Request $request): View|RedirectResponse
    {
        if (!session('auth.2fa_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $userId = session('auth.2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        // Rate limit: 5 intentos por usuario
        $key = '2fa.' . $userId;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => "Demasiados intentos. Esperá {$seconds} segundos."]);
        }

        $request->validate(['code' => 'required|string|size:6']);

        $twoFactorCode = TwoFactorCode::where('user_id', $userId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->where('code', $request->input('code'))
            ->latest()
            ->first();

        if (!$twoFactorCode) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['code' => 'Código incorrecto o expirado.']);
        }

        $twoFactorCode->markAsUsed();
        RateLimiter::clear($key);

        $user     = $twoFactorCode->user;
        $remember = session('auth.2fa_remember', false);

        $request->session()->forget(['auth.2fa_pending_user_id', 'auth.2fa_remember']);
        $request->session()->regenerate();

        Auth::login($user, $remember);

        $currentIp  = $request->ip();
        $previousIp = $user->last_login_ip;
        $user->update(['last_login_ip' => $currentIp, 'last_login_at' => now()]);
        if ($previousIp && $previousIp !== $currentIp) {
            try {
                app(SmtpEmailService::class)->sendNewLoginAlert($user->email, $user->name, $currentIp, $request->userAgent() ?? '');
            } catch (\Throwable) {}
        }

        return redirect()->intended(route('home', absolute: false));
    }

    public function resend(Request $request): RedirectResponse
    {
        $userId = session('auth.2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        // Rate limit reenvíos: 3 por 5 minutos
        $key = '2fa.resend.' . $userId;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['code' => 'Esperá unos minutos antes de pedir otro código.']);
        }
        RateLimiter::hit($key, 300);

        $user = \App\Models\User::find($userId);
        if (!$user) return redirect()->route('login');

        $code = TwoFactorCode::generateFor($user);

        try {
            app(SmtpEmailService::class)->send2FACode($user->email, $user->name, $code->code);
        } catch (\Throwable) {}

        return back()->with('status', 'Se envió un nuevo código a tu email.');
    }

    // ── Activar / Desactivar 2FA desde el perfil ──────────────────────────────

    public function enable(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['two_factor_enabled' => true]);

        // Enviar código de confirmación de activación
        $code = TwoFactorCode::generateFor($user);
        try {
            app(SmtpEmailService::class)->send2FACode($user->email, $user->name, $code->code);
        } catch (\Throwable) {}

        return back()->with('success', '2FA activado. Te enviamos un email de confirmación.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);
        $request->user()->update(['two_factor_enabled' => false]);

        return back()->with('success', 'Verificación en dos pasos desactivada.');
    }
}
