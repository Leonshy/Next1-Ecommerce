<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true; // Productos activos son públicos
    }

    public function view(?User $user, Product $product): bool
    {
        return $product->is_active || ($user && ($user->isAdmin() || $user->isVendedor()));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isVendedor();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isVendedor();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}
