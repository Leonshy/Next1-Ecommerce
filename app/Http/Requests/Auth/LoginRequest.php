<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\SmtpEmailService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $this->ensureHcaptchaValid();
        $this->ensureAccountIsNotLocked();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->recordFailedAttempt();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->lockoutKey());
    }

    protected function ensureAccountIsNotLocked(): void
    {
        $user = User::where('email', $this->string('email'))->first();

        if ($user && $user->isLocked()) {
            $minutes = (int) now()->diffInMinutes($user->locked_until, false);
            throw ValidationException::withMessages([
                'email' => "Tu cuenta está bloqueada temporalmente. Intentá de nuevo en {$minutes} minuto(s).",
            ]);
        }
    }

    protected function recordFailedAttempt(): void
    {
        $key = $this->lockoutKey();

        // 10 intentos en 15 minutos → bloquear cuenta 30 min y notificar
        RateLimiter::hit($key, 900); // decae a los 15 minutos

        if (RateLimiter::attempts($key) >= 10) {
            $user = User::where('email', $this->string('email'))->first();
            if ($user && ! $user->isLocked()) {
                $lockedUntil = now()->addMinutes(30);
                $user->update(['locked_until' => $lockedUntil]);

                try {
                    app(SmtpEmailService::class)->sendAccountLocked($user->email, $user->name, $lockedUntil);
                } catch (\Throwable) {}
            }
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function ensureHcaptchaValid(): void
    {
        $settings = \App\Models\HcaptchaSetting::first();
        if (!$settings || !$settings->is_enabled || !$settings->protect_login) return;

        if (!\App\Models\HcaptchaSetting::verifyToken($this->input('h-captcha-response'))) {
            throw ValidationException::withMessages([
                'email' => 'La verificación de seguridad falló. Intentá de nuevo.',
            ]);
        }
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    protected function lockoutKey(): string
    {
        return 'lockout.' . Str::lower($this->string('email'));
    }
}
