<?php
/**
 * Created by Vincent.
 * User: vincendev
 * Date: 18.09.2017
 * Time: 05:30
 */

namespace Vincendev\Laraflood;

use Illuminate\Support\ServiceProvider;

class LarafloodServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laraflood', function ($app) {
            return new Laraflood;
        });
    }
}