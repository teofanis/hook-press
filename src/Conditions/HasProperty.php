<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;

class HasProperty implements Condition
{
    /**
     * Arg can be:
     *  - string property name
     *  - array: ['name' => 'driver', 'public' => true, 'static' => false, 'type' => 'string|FQCN']
     *
     * @template T of object
     *
     * @param  ReflectionClass<T>  $ref
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        if (is_string($arg)) {
            return $ref->hasProperty($arg);
        }

        if (! is_array($arg) || empty($arg['name']) || ! is_string($arg['name'])) {
            return false;
        }

        if (! $ref->hasProperty($arg['name'])) {
            return false;
        }

        $p = $ref->getProperty($arg['name']);

        if (array_key_exists('public', $arg) && $p->isPublic() !== (bool) $arg['public']) {
            return false;
        }
        if (array_key_exists('protected', $arg) && $p->isProtected() !== (bool) $arg['protected']) {
            return false;
        }
        if (array_key_exists('private', $arg) && $p->isPrivate() !== (bool) $arg['private']) {
            return false;
        }
        if (array_key_exists('static', $arg) && $p->isStatic() !== (bool) $arg['static']) {
            return false;
        }

        if (! empty($arg['type']) && is_string($arg['type'])) {
            $type = $p->getType();
            $actual = $type ? ltrim((string) $type, '\\') : '';
            $expected = ltrim($arg['type'], '\\');
            if ($expected !== '' && $actual !== $expected) {
                return false;
            }
        }

        return true;
    }
}
