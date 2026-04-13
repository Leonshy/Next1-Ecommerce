<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        // Caso 1: ya tiene cuenta vinculada con este Google ID → login directo
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Actualizar avatar por si cambió
            $this->syncAvatar($user, $googleUser->getAvatar());
            Auth::login($user, remember: true);
            return redirect()->intended(route('home'));
        }

        // Caso 2: tiene cuenta con ese email → vincular Google ID
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update(['google_id' => $googleUser->getId()]);

            // Asegurarse de que tenga perfil y rol (por si fueron creados sin listener)
            Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'email'     => $user->email,
                    'full_name' => $user->name,
                ]
            );
            UserRole::firstOrCreate(['user_id' => $user->id, 'role' => 'usuario']);

            $this->syncAvatar($user, $googleUser->getAvatar());
            Auth::login($user, remember: true);
            return redirect()->intended(route('home'));
        }

        // Caso 3: usuario nuevo → crear cuenta
        $user = User::create([
            'name'              => $googleUser->getName(),
            'email'             => $googleUser->getEmail(),
            'google_id'         => $googleUser->getId(),
            'email_verified_at' => now(),
            'password'          => null,
        ]);

        // El listener CreateUserProfile crea el perfil y el rol automáticamente
        event(new Registered($user));

        // Guardar avatar de Google en el perfil recién creado
        $this->syncAvatar($user, $googleUser->getAvatar());

        Auth::login($user, remember: true);
        return redirect()->intended(route('home'));
    }

    /**
     * Guarda el avatar de Google en el perfil si todavía no tiene uno propio.
     */
    private function syncAvatar(User $user, ?string $avatarUrl): void
    {
        if (!$avatarUrl) return;

        $profile = $user->profile;

        if ($profile && empty($profile->avatar_url)) {
            $profile->update(['avatar_url' => $avatarUrl]);
        }
    }
}
