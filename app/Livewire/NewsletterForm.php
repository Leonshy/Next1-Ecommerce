<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use App\Services\HCaptchaService;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';
    public string $hcaptchaToken = '';
    public bool $submitted = false;
    public string $message = '';

    protected $rules = [
        'email' => 'required|email|max:255',
    ];

    public function submit(): void
    {
        $this->validate();

        $captchaService = app(HCaptchaService::class);

        if ($captchaService->isProtected('newsletter')) {
            $result = $captchaService->verify($this->hcaptchaToken);
            if (!$result['success']) {
                $this->addError('hcaptchaToken', 'Por favor completá el captcha.');
                return;
            }
        }

        $exists = NewsletterSubscriber::where('email', $this->email)->first();

        if ($exists) {
            $this->message   = '¡Ya estás suscrito!';
            $this->submitted = true;
            return;
        }

        NewsletterSubscriber::create([
            'email'         => $this->email,
            'status'        => 'pendiente',
            'subscribed_at' => now(),
        ]);

        $this->message   = '¡Gracias por suscribirte!';
        $this->submitted = true;
        $this->email     = '';
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
