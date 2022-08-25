<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('max_rows', function ($attribute, $value, $parameters, $validator) {
            $fp = file($value, FILE_SKIP_EMPTY_LINES);
            if(count($fp) < $parameters){
                true;
            }

            return false;

            // if (!empty($value) && (strlen($value) % 2) == 0) {
            //     return true;
            // }
            // return false;
        });
    }
}
