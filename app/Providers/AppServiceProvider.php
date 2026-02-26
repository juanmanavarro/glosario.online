<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Term;
use App\Models\TermVersion;
use App\Observers\TermObserver;
use App\Observers\TermVersionObserver;
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
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(config('filament-shield.super_admin.name', 'super_admin')) ? true : null;
        });

        Term::observe(TermObserver::class);
        TermVersion::observe(TermVersionObserver::class);
    }
}
