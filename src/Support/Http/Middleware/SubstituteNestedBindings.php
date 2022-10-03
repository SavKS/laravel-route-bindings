<?php

namespace Savks\LaravelRouteBindings\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use LogicException;
use Savks\LaravelRouteBindings\Support\BindingsRepository;

class SubstituteNestedBindings
{
    protected static array $nestedBindings = [];

    public function __construct(protected BindingsRepository $bindingsRepository)
    {
    }


    public function handle(Request $request, Closure $next): mixed
    {
        $route = $request->route();
        $parameters = $route->originalParameters();

        $parentParameters = [];

        foreach ($parameters as $name => $value) {
            if (Str::startsWith($name, '__')) {
                if (! $parentParameters) {
                    throw new LogicException("No parent parameter found for nested parameters \"{$name}\".");
                }

                $resolvedValue = $this->resolveNestedParameterFromParents(
                    $name,
                    $value,
                    $parentParameters,
                    $route
                );

                $route->setParameter($name, $resolvedValue);

                $parentParameters[$name] = $resolvedValue;
            } elseif (Str::contains($name, '__')) {
                $route->setParameter(
                    $name,
                    $this->resolveCombinedNestedParameter(
                        \explode('__', (string) $name),
                        $value,
                        $route->parameters(),
                        $route
                    )
                );

                $parentParameters = [];
            } else {
                $parentParameters = [
                    $name => $route->parameter($name),
                ];
            }
        }

        return $next($request);
    }

    public function resolveNestedParameterFromParents(
        string $name,
        mixed $value,
        array $parentParameters,
        Route $route
    ): mixed {
        $keyParts = [];

        foreach ($parentParameters as $parentParameterKey => $parentParameterValue) {
            $keyParts[] = $this->clearNestedBindKey($parentParameterKey);
        }

        $keyParts[] = $this->clearNestedBindKey($name);

        $key = \implode('.', $keyParts);

        if (! isset(static::$nestedBindings[$key])) {
            return $value;
        }

        return \call_user_func_array(
            static::$nestedBindings[$key],
            [...[$value], ...\array_values($parentParameters), ...[$route]]
        );
    }

    public function resolveCombinedNestedParameter(array $keyParts, mixed $value, array $parameters, Route $route): mixed
    {
        $key = \implode('.', $keyParts);

        if (! isset(static::$nestedBindings[$key])) {
            return $value;
        }

        \array_pop($keyParts);

        $arguments = [];

        foreach ($keyParts as $index => $keyPart) {
            $arguments[] = $parameters[$index !== 0 ? "__{$keyPart}" : $keyPart];
        }

        $currentParameters = [...[$value], ...$arguments, ...[$route]];

        return \call_user_func_array(
            static::$nestedBindings[$key],
            $currentParameters
        );
    }

    protected function clearNestedBindKey(string $name): string
    {
        return \preg_replace('/^_{2}/', '', $name);
    }
}
