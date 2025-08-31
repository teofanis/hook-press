<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class ExtendsClass implements Condition
{
    /**
     * @template T of object
     *
     * @param  ReflectionClass<T>  $ref
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        if (! is_string($arg) || $arg === '') {
            return false;
        }

        return $ref->isSubclassOf($arg);
    }
}
