<?php

declare(strict_types=1);

use HookPress\Contracts\Condition;
use HookPress\Support\Mapper;

// A tiny inline condition to match class short name by substring.
class NameContains implements Condition
{
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        $needle = (string) $arg;

        return $needle !== '' && str_contains($ref->getShortName(), $needle);
    }
}

beforeEach(function (): void {
    $this->writeClassmap();

    // Use custom FQCN condition
    config()->set('hook-press.maps', [
        'named_card' => [
            'namespaces' => ['App\\Classes\\PayoutMethods\\'],
            'conditions' => [
                NameContains::class => 'Card',
            ],
        ],
    ]);
});

it('supports custom condition classes by FQCN', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);
    $map = $mapper->build();

    $named = $map['named_card'] ?? [];

    expect($named)->toContain(\App\Classes\PayoutMethods\CardPayout::class)
        ->and($named)->not()->toContain(\App\Classes\PayoutMethods\BankPayout::class);
});
