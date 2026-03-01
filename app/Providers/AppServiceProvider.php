<?php

namespace App\Providers;

use App\Filament\Auth\Responses\LoginResponse as CustomFilamentLoginResponse;
use App\Models\User;
use App\Models\Term;
use App\Models\TermVersion;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponseContract;
use App\Observers\TermObserver;
use App\Observers\TermVersionObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilamentLoginResponseContract::class, CustomFilamentLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if (str_starts_with($appUrl, 'https://')) {
            URL::forceRootUrl($appUrl);
            URL::forceScheme('https');
        }

        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole(config('filament-shield.super_admin.name', 'super_admin')) ? true : null;
        });

        Term::observe(TermObserver::class);
        TermVersion::observe(TermVersionObserver::class);
    }
}
