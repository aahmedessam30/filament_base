<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect(Route::getRoutes()->getRoutesByName())
            ->keys()
            ->map(function ($route) {
                return explode(".", $route);
            })
            ->filter(function ($route) {
                return !in_array($route[0], ['sanctum', 'ignition', 'verification', 'auth']);
            })
            ->map(function ($route) {
                $reqVersion = strtolower(str_starts_with(request()->path(), 'api/')
                    ? (request()->header('api-version') ?? request()->segment(2))
                    : config('api_versions.current_version'));
                $versionsNames      = collect(config("api_versions.versions.$reqVersion.files"))->pluck('prefix');
                $defaultRoutesNames = collect(config('api_versions.default_files.files'))->pluck('prefix');
                $guard              = (in_array($route[0], array_merge($versionsNames->toArray(), $defaultRoutesNames->toArray()))
                    ? ($role = Role::where('name', $route[0])->first()) ? $role->guard_name : 'web'
                    : 'web');
                return "$guard." . implode("_", $route);
            })
            ->toArray();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => explode(".", $permission)[1], 'guard_name' => explode(".", $permission)[0]]);
        }
    }
}
