<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            if($value){
                return preg_match('/^[a-zA-Z\s]+$/', $value);
            }

            return true;
        });
        
        Validator::extend('alpha_num_symbols', function ($attribute, $value) {
            if($value){
                return preg_match('/^[a-zA-Z0-9!@#%$]+$/', $value);
            }

            return true;        
        });

        Validator::extend('uppercase', function ($attribute, $value) {
            return strtoupper($value) === $value;
        });
    }
}
