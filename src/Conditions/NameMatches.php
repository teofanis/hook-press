<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class NameMatches implements Condition
{
    /**
     * Arg can be:
     *  - string regex (applied to FQCN)
     *  - array: ['pattern' => '/Controller$/', 'short' => true]  // short => use short name
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        $pattern = null;
        $useShort = false;

        if (is_string($arg)) {
            $pattern = $arg;
        } elseif (is_array($arg) && is_string($arg['pattern'] ?? null)) {
            $pattern = $arg['pattern'];
            $useShort = (bool) ($arg['short'] ?? false);
        } else {
            return false;
        }

        $target = $useShort ? $ref->getShortName() : $ref->getName();

        return @preg_match($pattern, $target) === 1;
    }
}
