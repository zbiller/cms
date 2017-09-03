<?php

namespace App\Providers;

use App\Models\Auth\Permission;
use Blade;
use Cache;
use Exception;
use Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Log;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerGates();
        $this->registerBladeIfs();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register gates for acl permissions.
     *
     * @return bool
     */
    protected function registerGates()
    {
        try {
            Cache::rememberForever('acl', function () {
                return Permission::with('roles')->get();
            })->map(function ($permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            });

            return true;
        } catch (Exception $e) {
            Log::alert("Could not register permissions because {$e->getMessage()}" . PHP_EOL . $e->getTraceAsString());
            
            return false;
        }
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
