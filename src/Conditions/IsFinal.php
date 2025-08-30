<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class IsFinal implements Condition
{
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        return $ref->isFinal();
    }
}
