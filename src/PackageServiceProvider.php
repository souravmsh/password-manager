<?php 

namespace Souravmsh\PasswordManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;

use Illuminate\Contracts\Http\Kernel;
use Souravmsh\PasswordManager\Http\Middleware\PasswordExpiryCheck;


class PackageServiceProvider extends ServiceProvider
{
    public function boot(Kernel $kernel)
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadViewsFrom(__DIR__.'/views', 'password-manager');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/password-manager'),
        ]);

        $this->publishes([
            __DIR__.'/config/password-manager.php' => config_path('password-manager.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations')
        ], 'migrations');

        // required * fields
        Blade::directive(
            'required',
            function ($expression) {
                return '<span class="text-danger">*</span>';
            }
        );

        // push middleware - after
        $kernel->pushMiddleware(PasswordExpiryCheck::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/password-manager.php', 'password-manager');
    } 
}