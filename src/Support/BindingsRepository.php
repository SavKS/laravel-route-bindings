<?php

namespace Savks\LaravelRouteBindings\Support;

use Savks\LaravelRouteBindings\Enums\BindTypes;

class BindingsRepository
{
    protected array $bindings = [];

    public function registerModelBind(string $key, string $modelFQN, string $column = 'id'): static
    {
        $this->bindings[$key] = [
            'type' => BindTypes::MODEL,
            'key' => $key,
            'args' => [
                'modelFQN' => $modelFQN,
                'column' => $column,
            ],
        ];

        return $this;
    }

    public function registerBind(string $key, callable $resolver): static
    {
        $this->bindings[$key] = [
            'type' => BindTypes::CUSTOM,
            'key' => $key,
            'args' => [
                'resolver' => $resolver,
            ],
        ];

        return $this;
    }

    public function registerNestedBind(string $key, callable $resolver): static
    {
        $this->bindings[$key] = [
            'type' => BindTypes::NESTED,
            'key' => $key,
            'args' => [
                'resolver' => $resolver,
            ],
        ];

        return $this;
    }

    public function tryFind(string $key): ?array
    {
        return $this->bindings[$key] ?? null;
    }
}
