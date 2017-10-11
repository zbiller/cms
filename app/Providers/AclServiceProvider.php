<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBladeIfs();
    }

    /**
     * Register blade directives for acl roles.
     *
     * @return void
     */
    protected function registerBladeIfs()
    {
        Blade::{'if'}('developer', function () {
            return auth()->check() && auth()->user()->isDeveloper();
        });

        Blade::{'if'}('permission', function ($permission) {
            return auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasPermission($permission));
        });

        Blade::{'if'}('haspermission', function ($permission) {
            return auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasPermission($permission));
        });

        Blade::{'if'}('hasanypermission', function ($permissions) {
            return auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasAnyPermission($permissions));
        });

        Blade::{'if'}('hasallpermissions', function ($permissions) {
            return auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasAllPermissions($permissions));
        });

        Blade::{'if'}('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::{'if'}('hasrole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::{'if'}('hasanyrole', function ($roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        Blade::{'if'}('hasallroles', function ($roles) {
            return auth()->check() && auth()->user()->hasAllRoles($roles);
        });
    }
}
