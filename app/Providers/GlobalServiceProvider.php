<?php

namespace App\Providers;

use App\Library\Services\Glob;
use Illuminate\Support\ServiceProvider;

class GlobalServiceProvider extends ServiceProvider {
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('App\Library\Helper', function ($app) {
            return new Glob();
        });
    }
}