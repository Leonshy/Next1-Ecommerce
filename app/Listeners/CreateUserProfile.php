<?php

namespace App\Listeners;

use App\Models\Profile;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;

class CreateUserProfile
{
    // Equivalente al trigger handle_new_user de Supabase
    public function handle(Registered $event): void
    {
        $user = $event->user;

        Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email'     => $user->email,
                'full_name' => $user->name,
            ]
        );

        UserRole::firstOrCreate(
            ['user_id' => $user->id, 'role' => 'usuario']
        );
    }
}
