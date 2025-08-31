<?php

declare(strict_types=1);

namespace HookPress\Conditions;

use HookPress\Contracts\Condition;
use ReflectionClass;
use ReflectionException;

class HasMethod implements Condition
{
    /**
     * Arg can be:
     *  - string method name
     *  - array: ['name' => 'handle', 'public' => true, 'static' => false, 'returns' => 'void|FQCN']
     *
     * @template T of object
     *
     * @param  ReflectionClass<T>  $ref
     */
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        if (is_string($arg)) {
            return $ref->hasMethod($arg);
        }

        if (! is_array($arg) || empty($arg['name']) || ! is_string($arg['name'])) {
            return false;
        }

        if (! $ref->hasMethod($arg['name'])) {
            return false;
        }

        try {
            $m = $ref->getMethod($arg['name']);
        } catch (ReflectionException) {
            return false;
        }

        if (array_key_exists('public', $arg) && $m->isPublic() !== (bool) $arg['public']) {
            return false;
        }
        if (array_key_exists('protected', $arg) && $m->isProtected() !== (bool) $arg['protected']) {
            return false;
        }
        if (array_key_exists('private', $arg) && $m->isPrivate() !== (bool) $arg['private']) {
            return false;
        }
        if (array_key_exists('static', $arg) && $m->isStatic() !== (bool) $arg['static']) {
            return false;
        }

        if (! empty($arg['returns']) && is_string($arg['returns'])) {
            $type = $m->getReturnType();
            $actual = $type ? ltrim((string) $type, '\\') : '';
            $expected = ltrim($arg['returns'], '\\');
            if ($expected !== '' && $actual !== $expected) {
                return false;
            }
        }

        return true;
    }
}
