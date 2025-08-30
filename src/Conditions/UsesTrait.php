<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class UsesTrait implements Condition
{
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        if (! is_string($arg) || $arg === '') {
            return false;
        }

        return in_array($arg, $ref->getTraitNames(), true);
    }
}
