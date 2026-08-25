<?php

declare(strict_types=1);

namespace App\Classes\Types;

use App\Interfaces\PayoutMethod;

/**
 * Every type shape TypeName has to render, so the unit test can assert it
 * against the string PHP itself would produce.
 *
 * Not registered in the fake classmap — discovery must not pick it up.
 */
class TypeShowcase
{
    public string $scalar = '';

    public ?int $nullableScalar = null;

    public mixed $anything = null;

    public int|string $union = 0;

    public int|string|null $nullableUnion = null;

    public ?PayoutMethod $nullableClass = null;

    public $untyped;

    public function scalar(): string
    {
        return '';
    }

    public function nullable(): ?int
    {
        return null;
    }

    public function anything(): mixed
    {
        return null;
    }

    public function nothing(): void {}

    public function onlyNull(): null
    {
        return null;
    }

    public function union(): int|string
    {
        return 0;
    }

    public function nullableUnion(): int|string|null
    {
        return null;
    }

    public function intersection(): Alpha&Beta
    {
        throw new \LogicException('never called');
    }

    public function nullableClass(): ?PayoutMethod
    {
        return null;
    }

    public function untyped() {}
}
