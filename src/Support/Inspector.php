<?php

declare(strict_types=1);

namespace HookPress\Support;

use ReflectionClass;

class Inspector
{
    /**
     * Safe Reflection Factory
     *
     * @return ReflectionClass<object>|null
     */
    public function reflect(string $class): ?ReflectionClass
    {
        if (! class_exists($class, true) && ! interface_exists($class, true) && ! trait_exists($class, true)) {
            return null;
        }

        return new ReflectionClass($class);
    }

    /**
     * @param  string|class-string  $class
     */
    public function isTrait(string $class): bool
    {
        $ref = $this->reflect($class);

        return $ref?->isTrait() ?? false;
    }
}
