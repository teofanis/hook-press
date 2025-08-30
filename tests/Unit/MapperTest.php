<?php

declare(strict_types=1);

use HookPress\Support\Mapper;

beforeEach(fn () => $this->writeClassmap());

it('builds maps and groups by traits', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);

    $map = $mapper->build();

    expect($map['payout_methods'])->toContain(
        \App\Classes\PayoutMethods\CardPayout::class,
        \App\Classes\PayoutMethods\BankPayout::class
    );

    expect($map['payout_methods'])->not()->toContain(\App\Classes\PayoutMethods\AbstractBase::class);

    expect($map['attributed'])->toContain(\App\Classes\Attributed\Marked::class);

    $traitsKey = config('hook-press.traits.group_key', 'traits');
    expect($map[$traitsKey][\App\Traits\Searchable::class] ?? [])->toContain(\App\Classes\PayoutMethods\CardPayout::class);
});

it('does not load anything when conditions are omitted', function (): void {
    $this->writeClassmap();

    // Add a new map without conditions
    $maps = config('hook-press.maps');
    $maps['instantiables'] = [
        'namespaces' => ['App\\Classes\\PayoutMethods\\'],
        // no 'conditions' key
    ];
    config()->set('hook-press.maps', $maps);

    $mapper = app(\HookPress\Support\Mapper::class);
    $map = $mapper->build();
    expect($map['instantiables'] ?? [])->toBe([]);
    expect($map['payout_methods'])->toContain(
        App\Classes\PayoutMethods\BankPayout::class,
        App\Classes\PayoutMethods\CardPayout::class
    )->not()->toContain(App\Classes\PayoutMethods\AbstractBase::class);
});
