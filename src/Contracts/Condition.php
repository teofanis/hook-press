<?php

declare(strict_types=1);

namespace HookPress\Contracts;

use ReflectionClass;

interface Condition
{
    /**
     * Determine if the reflection target passes this condition.
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool;
}
