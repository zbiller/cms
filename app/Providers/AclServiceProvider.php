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
            $blade->directive('role', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });

            $blade->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasrole', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });

            $blade->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasanyrole', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$roles})): ?>";
            });

            $blade->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            $blade->directive('hasallroles', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAllRoles({$roles})): ?>";
            });

            $blade->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
