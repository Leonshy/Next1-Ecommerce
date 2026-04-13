<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('admin', fn(User $user) => $user->isAdmin());
        Gate::define('vendedor', fn(User $user) => $user->isVendedor() || $user->isAdmin());
        Gate::define('manage-products', fn(User $user) => $user->isAdmin() || $user->isVendedor());
    }
}
