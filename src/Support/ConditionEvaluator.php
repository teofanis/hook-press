<?php

declare(strict_types=1);

namespace HookPress\Support;

use HookPress\Contracts\Condition;
use Illuminate\Contracts\Container\Container;
use ReflectionClass;

class ConditionEvaluator
{
    /**
     * @param  array<string,Condition>  $builtIns
     */
    public function __construct(
        protected Container $app,
        protected array $builtIns = []
    ) {}

    /**
     * @param  array<int|string,mixed>  $conditions
     */
    public function passes(ReflectionClass $ref, array $conditions): bool
    {
        if ($conditions === []) {
            return false;
        }
        foreach ($conditions as $key => $value) {
            [$name, $arg] = is_int($key) ? [$value, null] : [$key, $value];

            $condition = $this->resolve($name);

            if (! $condition instanceof \HookPress\Contracts\Condition || ! $condition->passes($ref, $arg)) {
                return false;
            }
        }

        return true;
    }

    protected function resolve(string $name): ?Condition
    {
        if (isset($this->builtIns[$name])) {
            return $this->builtIns[$name];
        }

        // Allow resolving user-defined conditions by FQCN
        if (class_exists($name)) {
            $instance = $this->app->make($name);

            return $instance instanceof Condition ? $instance : null;
        }

        return null;
    }
}
