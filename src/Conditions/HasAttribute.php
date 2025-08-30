<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class HasAttribute implements Condition
{
    public function passes(ReflectionClass $ref, mixed $attribute = null): bool
    {
        if (! is_string($attribute) || $attribute === '') {
            return false;
        }

        return $ref->getAttributes($attribute) !== [];
    }
}
