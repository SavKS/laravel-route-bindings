<?php

namespace Savks\LaravelRouteBindings\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Savks\LaravelRouteBindings\Support\BindingsRepository;
use Savks\LaravelRouteBindings\Support\RouterContext;
use Savks\PhpContexts\Context;

class LaravelRouteBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Router::macro('localModel', function (string $key, string $modelFQN) {
            $context = Context::use(RouterContext::class);

            $context->modelBind($key, $modelFQN);
        });

        Router::macro('localBind', function (string $key, callable $resolver) {
            $context = Context::use(RouterContext::class);

            $context->bind($key, $resolver);
        });

        Router::macro('localNestedBind', function (string $key, callable $resolver) {
            $context = Context::use(RouterContext::class);

            $context->nestedBind($key, $resolver);
        });

        $this->app->singleton(BindingsRepository::class);
    }
}
