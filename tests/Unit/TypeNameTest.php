<?php

declare(strict_types=1);

use App\Classes\Types\DnfShowcase;
use App\Classes\Types\TypeShowcase;
use HookPress\Conditions\HasMethod;
use HookPress\Conditions\HasProperty;
use HookPress\Support\TypeName;

/**
 * @return array<string,string>
 */
function returnTypeNames(string $class): array
{
    $names = [];

    foreach ((new ReflectionClass($class))->getMethods() as $method) {
        $names[$method->getName()] = TypeName::from($method->getReturnType());
    }

    return $names;
}

it('renders every return type shape the way PHP does', function (): void {
    expect(returnTypeNames(TypeShowcase::class))->toBe([
        'scalar' => 'string',
        'nullable' => '?int',
        'anything' => 'mixed',
        'nothing' => 'void',
        'onlyNull' => 'null',
        'union' => 'string|int',
        'nullableUnion' => 'string|int|null',
        'intersection' => 'App\Classes\Types\Alpha&App\Classes\Types\Beta',
        'nullableClass' => '?App\Interfaces\PayoutMethod',
        'untyped' => '',
    ]);
});

it('renders property types', function (): void {
    $ref = new ReflectionClass(TypeShowcase::class);

    $names = [];
    foreach ($ref->getProperties() as $property) {
        $names[$property->getName()] = TypeName::from($property->getType());
    }

    expect($names)->toBe([
        'scalar' => 'string',
        'nullableScalar' => '?int',
        'anything' => 'mixed',
        'union' => 'string|int',
        'nullableUnion' => 'string|int|null',
        'nullableClass' => '?App\Interfaces\PayoutMethod',
        'untyped' => '',
    ]);
});

it('parenthesises the intersection inside a DNF type', function (): void {
    expect(returnTypeNames(DnfShowcase::class))->toBe([
        'dnf' => '(App\Classes\Types\Alpha&App\Classes\Types\Beta)|string',
    ]);
})->skip(PHP_VERSION_ID < 80200, 'DNF types require PHP 8.2.');

it('returns an empty string when there is no type at all', function (): void {
    expect(TypeName::from(null))->toBe('');
});

it('matches types through the HasMethod condition', function (): void {
    $ref = new ReflectionClass(TypeShowcase::class);
    $condition = new HasMethod;

    expect($condition->passes($ref, ['name' => 'nullable', 'returns' => '?int']))->toBeTrue()
        ->and($condition->passes($ref, ['name' => 'nullable', 'returns' => 'int']))->toBeFalse()
        ->and($condition->passes($ref, ['name' => 'union', 'returns' => 'string|int']))->toBeTrue()
        // Leading backslashes are trimmed on both sides.
        ->and($condition->passes($ref, [
            'name' => 'nullableClass',
            'returns' => '?App\Interfaces\PayoutMethod',
        ]))->toBeTrue()
        // An untyped method has no type to match against.
        ->and($condition->passes($ref, ['name' => 'untyped', 'returns' => 'string']))->toBeFalse();
});

it('matches types through the HasProperty condition', function (): void {
    $ref = new ReflectionClass(TypeShowcase::class);
    $condition = new HasProperty;

    expect($condition->passes($ref, ['name' => 'nullableScalar', 'type' => '?int']))->toBeTrue()
        ->and($condition->passes($ref, ['name' => 'nullableScalar', 'type' => 'int']))->toBeFalse()
        ->and($condition->passes($ref, ['name' => 'nullableUnion', 'type' => 'string|int|null']))->toBeTrue()
        ->and($condition->passes($ref, ['name' => 'untyped', 'type' => 'string']))->toBeFalse();
});
