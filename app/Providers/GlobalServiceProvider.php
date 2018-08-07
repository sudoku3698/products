<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\FileOperations;

class GlobalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\FileOperations', function ($app) {
          return new FileOperations();
        });
    }
}
