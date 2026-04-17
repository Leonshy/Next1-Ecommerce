<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function verify(string $token): RedirectResponse
    {
        // Token: solo alfanumérico, 64 chars exactos
        if (!preg_match('/^[a-zA-Z0-9]{64}$/', $token)) {
            return redirect()->route('home')
                ->with('error', 'Enlace de verificación inválido.');
        }

        $verified = NewsletterSubscriber::verifyByToken($token);

        if ($verified) {
            return redirect()->route('home')
                ->with('success', '¡Tu suscripción fue confirmada! Bienvenido/a a nuestro newsletter.');
        }

        return redirect()->route('home')
            ->with('error', 'El enlace de verificación es inválido o ya expiró. Podés suscribirte nuevamente desde el footer.');
    }
}
