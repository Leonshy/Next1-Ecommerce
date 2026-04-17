<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use App\Services\HCaptchaService;
use App\Services\SmtpEmailService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email          = '';
    public string $hcaptchaToken  = '';
    public bool   $submitted      = false;

    protected $rules = [
        'email' => 'required|email:rfc,dns|max:254',
    ];

    public function submit(): void
    {
        $this->validate();

        // Rate limiting: máx 3 intentos por email+IP por hora
        $key = 'newsletter.' . sha1(strtolower($this->email) . '|' . request()->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->submitted = true; // Mostrar éxito de todas formas (no revelar throttling)
            return;
        }
        RateLimiter::hit($key, 3600);

        // hCaptcha (si está habilitado para newsletter)
        $captchaService = app(HCaptchaService::class);
        if ($captchaService->isProtected('newsletter')) {
            $result = $captchaService->verify($this->hcaptchaToken);
            if (!$result['success']) {
                $this->addError('hcaptchaToken', 'Por favor completá el captcha.');
                return;
            }
        }

        $token = NewsletterSubscriber::subscribe($this->email);

        // Enviar email de verificación si se generó un token nuevo
        if ($token) {
            try {
                app(SmtpEmailService::class)->sendNewsletterVerification($this->email, $token);
            } catch (\Throwable) {
                // No fallar la suscripción si el email falla
            }
        }

        $this->submitted = true;
        $this->email     = '';
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
