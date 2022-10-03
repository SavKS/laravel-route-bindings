<?php

namespace Savks\LaravelRouteBindings\Providers;

use Illuminate\Support\ServiceProvider;
use Route;

class LaravelRouteBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Route::macro('localModel', function (string $modelFQN) {
            dd(1);
        });
    }
}
