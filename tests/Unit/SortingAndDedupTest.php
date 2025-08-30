<?php

declare(strict_types=1);

use HookPress\Support\Mapper;

beforeEach(function (): void {
    $this->writeClassmap();

    config()->set('hook-press.maps.payout_methods.namespaces', [
        'App\\Classes\\PayoutMethods\\',
        'App\\Classes\\PayoutMethods\\',
    ]);
});

it('ensures sorted and unique class lists', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);
    $map = $mapper->build();

    $methods = $map['payout_methods'] ?? [];

    expect($methods)->toEqual(array_values(array_unique($methods)));

    // Check alphabetical sort (string sort)
    $sorted = $methods;
    sort($sorted);
    expect($methods)->toEqual($sorted);
});
