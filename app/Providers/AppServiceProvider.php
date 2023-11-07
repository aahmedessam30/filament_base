<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Facades\Authentication\Authentication::class,
            \App\Services\Authentication\AuthenticationService::class
        );

        $this->app->bind(
            \App\Facades\Authentication\ResetPassword::class,
            \App\Services\Authentication\ResetPasswordService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
