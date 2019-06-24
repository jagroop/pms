<?php

namespace App\Providers;

use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
        
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

         $loader->alias('Notifications', \App\Facades\Notifications::class);

         $this->app->singleton('NotificationService', function ($app) {

           return app(\App\Services\NotificationService::class);

         });
    }
}
