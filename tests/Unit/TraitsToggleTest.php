<?php

declare(strict_types=1);

use HookPress\Support\Mapper;

beforeEach(function (): void {
    $this->writeClassmap();
    config()->set('hook-press.traits.enabled', false);
});

it('does not include trait group when disabled', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);
    $map = $mapper->build();

    $traitsKey = config('hook-press.traits.group_key', 'traits');
    expect(array_key_exists($traitsKey, $map))->toBeFalse();
});
