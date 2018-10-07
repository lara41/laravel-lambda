<?php

namespace Lara41\Utils;

use Illuminate\Support\ServiceProvider;
use Lara41\Utils\Commands\GenerateZip;

class UtilsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateZip::class
            ]);
        }
    }
}
