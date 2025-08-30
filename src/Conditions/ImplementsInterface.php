<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class ImplementsInterface implements Condition
{
    public function passes(ReflectionClass $ref, mixed $interface = null): bool
    {
        if (! is_string($interface) || $interface === '') {
            return false;
        }

        return $ref->implementsInterface($interface);
    }
}
