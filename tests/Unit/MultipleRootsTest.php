declare(strict_types=1);

declare(strict_types=1);

declare(strict_types=1);


<?php

use HookPress\Support\Mapper;

beforeEach(function (): void {
    $this->writeClassmap();

    config()->set('hook-press.roots', [
        'App\\',
        'Domain\\', // additional root; won’t match our fixtures but tests merging logic
    ]);
});

it('handles multiple roots without errors', function (): void {
    /** @var Mapper $mapper */
    $mapper = app(Mapper::class);
    $map = $mapper->build();

    // Regular assertions still pass
    expect($map['payout_methods'] ?? [])->toContain(
        \App\Classes\PayoutMethods\CardPayout::class,
        \App\Classes\PayoutMethods\BankPayout::class
    );
});
