<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $this->mapApiRoutes();

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Handle Api routes.
     */
    protected function mapApiRoutes(): void
    {
        $this->mapDefaultApiRoutes();

        $this->mapApiVersionsRoutes();
    }

    /**
     * Handle Api versions routes.
     */
    protected function mapApiVersionsRoutes(): void
    {
        $reqVersion = strtolower(str_starts_with(request()->path(), 'api/')
            ? (request()->header('api-version') ?? request()->segment(2))
            : config('api_versions.current_version'));

        if (!is_null(config("api_versions.versions.$reqVersion.files"))) {
            foreach (config("api_versions.versions.$reqVersion.files") as $version) {
                $middleware = isset($version['middleware'])
                    ? array_merge(config("api_versions.versions.$reqVersion.middlewares"), (array)$version['middleware'])
                    : config("api_versions.versions.$reqVersion.middlewares");

                $route = Route::middleware($middleware)
                    ->prefix("api/$reqVersion/{$version['prefix']}");

                if (isset($version['as'])) {
                    $route->name("{$version['as']}.");
                }

                if (isset($version['namespace'])) {
                    $route->namespace($version['namespace']);
                }

                $route->group(base_path("routes/api/$reqVersion/{$version['name']}.php"));
            }
        }
    }


    /**
     * Handle Default Api routes.
     */
    protected function mapDefaultApiRoutes(): void
    {
        foreach (config('api_versions.default_files.files') as $file) {
            $routes = Route::middleware(config("api_versions.default_files.middlewares"))
                ->prefix("api/{$file['prefix']}");

            if (isset($file['as'])) {
                $routes->as("{$file['name']}.");
            }

            if (isset($file['namespace'])) {
                $routes->namespace($file['namespace']);
            }

            $routes->group(base_path("routes/api/{$file['name']}.php"));
        }
    }
}
