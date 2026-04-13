<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// Policy genérica para recursos que sólo maneja el admin
class AdminPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
