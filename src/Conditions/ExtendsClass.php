<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class ExtendsClass implements Condition
{
    public function passes(ReflectionClass $ref, mixed $parent = null): bool
    {
        return is_string($parent) && $parent !== '' && $ref->isSubclassOf($parent);
    }
}
