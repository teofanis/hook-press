<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class IsInstantiable implements Condition
{
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        return $ref->isInstantiable();
    }
}
