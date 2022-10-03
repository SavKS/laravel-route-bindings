<?php

namespace Savks\LaravelRouteBindings\Support;

use Savks\PhpContexts\Context;

class RouterContext extends Context
{
    protected BindingsRepository $bindingsRepository;

    public function __construct()
    {
        $this->bindingsRepository = new BindingsRepository();
    }

    public function modelBind(string $key, string $modelFQN): static
    {
        $this->bindingsRepository->registerModelBind($key, $modelFQN);

        return $this;
    }

    public function bind(string $key, callable $resolver): static
    {
        $this->bindingsRepository->registerBind($key, $resolver);

        return $this;
    }

    public function nestedBind(string $key, callable $resolver): static
    {
        $this->bindingsRepository->registerNestedBind($key, $resolver);

        return $this;
    }
}
