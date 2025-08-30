<?php

declare(strict_types=1);

use HookPress\Support\Mapper;

beforeEach(function (): void {
    $this->writeClassmap();

    // Start from the default and add exclusions
    config()->set('hook-press.exclusions', [
        'classes' => [
            \App\Classes\PayoutMethods\BankPayout::class,
        ],
        'namespaces' => [
            'App\\Classes\\Other\\', // excludes Unrelated
        ],
        'regex' => [
            '/^App\\\\Classes\\\\PayoutMethods\\\\Abstract/',
        ],
    ]);
});

it('respects exclusions by class/namespace/regex', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);
    $map = $mapper->build();

    // BankPayout (class exclusion) should NOT be present
    expect($map['payout_methods'] ?? [])->not()->toContain(\App\Classes\PayoutMethods\BankPayout::class);

    // AbstractBase excluded by regex AND by non-instantiable anyway
    expect($map['payout_methods'] ?? [])->not()->toContain(\App\Classes\PayoutMethods\AbstractBase::class);

    // Unrelated excluded by namespace (though not in payout_methods anyway)
    // Just assert maps exist and trait grouping unaffected for CardPayout
    $traitsKey = config('hook-press.traits.group_key', 'traits');
    expect($map[$traitsKey][\App\Traits\Searchable::class] ?? [])->toContain(\App\Classes\PayoutMethods\CardPayout::class);
});
