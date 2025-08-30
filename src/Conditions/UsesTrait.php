<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class UsesTrait implements Condition
{
    public function passes(ReflectionClass $ref, mixed $trait = null): bool
    {
        if (! is_string($trait) || $trait === '') {
            return false;
        }

        return in_array($trait, $ref->getTraitNames(), true);
    }
}
