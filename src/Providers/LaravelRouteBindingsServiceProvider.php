<?php

namespace Savks\LaravelRouteBindings\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelRouteBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        dde(1);
        Router::macro('localModel', function (string $key, string $modelFQN) {
            dd(1);
        });
    }
}
