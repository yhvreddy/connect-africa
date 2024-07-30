<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Helpers\TruFlix;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        foreach (glob(app_path() . '/Http/Helpers/*.php') as $file) {
            require_once($file);
        }

        // Register the helper class in the Service Container
        $this->app->singleton('truFlix', function () {
            return new truFlix();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
