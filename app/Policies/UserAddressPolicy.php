<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id || $user->isAdmin();
    }

    public function delete(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id || $user->isAdmin();
    }
}
