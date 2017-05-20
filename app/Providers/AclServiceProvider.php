<?php

namespace App\Providers;

use Gate, Cache, Log, Exception;
use App\Models\Auth\Permission;
use Illuminate\View\Compilers\BladeCompiler;
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
        $this->registerGates();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBladeExtensions();
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
    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $blade) {
            $blade->directive('permission', function ($permission) {
                return "<?php if(auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasPermission({$permission}))): ?>";
            });

            $blade->directive('elsepermission', function () {
                return "<?php else: ?>";
            });

            $blade->directive('endpermission', function () {
                return "<?php endif; ?>";
            });

            $blade->directive('haspermission', function ($permission) {
                return "<?php if(auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasPermission({$permission}))): ?>";
            });

            $blade->directive('elsehaspermission', function () {
                return "<?php else: ?>";
            });

            $blade->directive('endhaspermission', function () {
                return "<?php endif; ?>";
            });

            $blade->directive('hasanypermission', function ($permissions) {
                return "<?php if(auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasAnyPermission({$permissions}))): ?>";
            });

            $blade->directive('elsehasanypermission', function () {
                return "<?php else: ?>";
            });

            $blade->directive('endhasanypermission', function () {
                return "<?php endif; ?>";
            });

            $blade->directive('hasallpermissions', function ($permissions) {
                return "<?php if(auth()->check() && (auth()->user()->isDeveloper() || auth()->user()->hasAllPermissions({$permissions}))): ?>";
            });

            $blade->directive('elsehasallpermissions', function () {
                return "<?php else: ?>";
            });

            $blade->directive('endhasallpermissions', function () {
                return "<?php endif; ?>";
            });

            $blade->directive('role', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });

            $blade->directive('elserole', function () {
                return '<?php else: ?>';
            });

            $blade->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasrole', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });

            $blade->directive('elsehasrole', function () {
                return '<?php else: ?>';
            });

            $blade->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasanyrole', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$roles})): ?>";
            });

            $blade->directive('elsehasanyrole', function () {
                return '<?php else: ?>';
            });

            $blade->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasallroles', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAllRoles({$roles})): ?>";
            });

            $blade->directive('elsehasallroles', function () {
                return '<?php else: ?>';
            });

            $blade->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
