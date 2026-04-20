<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\TwoFactorCode;
use App\Services\SmtpEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Si el usuario tiene 2FA activo, interceptar antes de completar el login
        if ($user->two_factor_enabled) {
            $remember = $request->boolean('remember');
            Auth::logout();

            $code = TwoFactorCode::generateFor($user);
            try {
                app(SmtpEmailService::class)->send2FACode($user->email, $user->name, $code->code);
            } catch (\Throwable) {}

            $request->session()->regenerate();
            session(['auth.2fa_pending_user_id' => $user->id, 'auth.2fa_remember' => $remember]);

            return redirect()->route('2fa.challenge');
        }

        $request->session()->regenerate();

        $this->recordLoginAndNotify($request, $user);

        return redirect()->intended(route('home', absolute: false));
    }

    private function recordLoginAndNotify(Request $request, $user): void
    {
        $currentIp   = $request->ip();
        $previousIp  = $user->last_login_ip;

        $user->update([
            'last_login_ip' => $currentIp,
            'last_login_at' => now(),
            'locked_until'  => null, // limpiar bloqueos anteriores al loguear con éxito
        ]);

        // Notificar si la IP cambió (y el usuario ya tenía un login previo registrado)
        if ($previousIp && $previousIp !== $currentIp) {
            try {
                app(SmtpEmailService::class)->sendNewLoginAlert(
                    $user->email,
                    $user->name,
                    $currentIp,
                    $request->userAgent() ?? ''
                );
            } catch (\Throwable) {}
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
