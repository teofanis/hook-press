<?php

declare(strict_types=1);

namespace HookPress\Support;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

/**
 * Renders a reflected type exactly as ReflectionType::__toString() does,
 * without calling it — that method has been deprecated since PHP 8.0.
 */
final class TypeName
{
    public static function from(?ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType) {
            return self::named($type);
        }

        if ($type instanceof ReflectionIntersectionType) {
            return self::intersection($type);
        }

        if ($type instanceof ReflectionUnionType) {
            return self::union($type);
        }

        return '';
    }

    private static function named(ReflectionNamedType $type): string
    {
        $name = $type->getName();

        // `null` and `mixed` are nullable by definition, so PHP never prefixes them.
        return $type->allowsNull() && $name !== 'null' && $name !== 'mixed'
            ? '?'.$name
            : $name;
    }

    private static function union(ReflectionUnionType $type): string
    {
        $parts = [];

        foreach ($type->getTypes() as $member) {
            // A DNF type nests an intersection inside a union: (A&B)|C.
            $parts[] = $member instanceof ReflectionIntersectionType
                ? '('.self::from($member).')'
                : self::from($member);
        }

        return implode('|', $parts);
    }

    private static function intersection(ReflectionIntersectionType $type): string
    {
        $parts = [];

        foreach ($type->getTypes() as $member) {
            $parts[] = self::from($member);
        }

        return implode('&', $parts);
    }
}
