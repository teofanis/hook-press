<?php

declare(strict_types=1);

namespace HookPress\Support;

use ReflectionClass;
use ReflectionException;

class Inspector
{
    /**
     * Safe Reflection Factory
     */
    public function reflect(string $class): ?ReflectionClass
    {
        if (! class_exists($class, true) && ! interface_exists($class, true) && ! trait_exists($class, true)) {
            return null;
        }

        try {
            return new ReflectionClass($class);
        } catch (ReflectionException) {
            return null;
        }
    }

    public function isTrait(string $class): bool
    {
        $ref = $this->reflect($class);

        return $ref?->isTrait() ?? false;
    }
}
