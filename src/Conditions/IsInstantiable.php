<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class IsInstantiable implements Condition
{
    /**
     * @template T of object
     *
     * @param  ReflectionClass<T>  $ref
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        return $ref->isInstantiable();
    }
}
