<?php

namespace App\Providers;

use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-transaction', fn (User $user, MembershipTransaction $transaction): bool => $user->can_manage_members || $transaction->member->user_id === $user->id);
        Gate::define('manage-membership', fn (User $user): bool => (bool) $user->can_manage_members);
    }
}
