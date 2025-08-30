<?php

declare(strict_types=1);

use HookPress\Facades\HookPress;

beforeEach(fn () => $this->writeClassmap());

it('refreshes, reads, and filters maps via facade', function (): void {
    $built = HookPress::refresh();

    expect($built)->toBeArray()
        ->and(HookPress::map())->toBeArray();

    $methods = HookPress::map('payout_methods');
    expect($methods)->toContain(\App\Classes\PayoutMethods\CardPayout::class);

    $searchables = HookPress::classesUsing(\App\Traits\Searchable::class);
    expect($searchables)->toContain(\App\Classes\PayoutMethods\CardPayout::class);
    HookPress::clear();
    expect(HookPress::map())->toEqual([]);
});

it('returns empty arrays for unknown type or trait', function (): void {
    HookPress::refresh();

    expect(HookPress::map('unknown_type'))->toEqual([]);

    $unknownTrait = 'App\\Traits\\DoesNotExist';
    expect(HookPress::classesUsing($unknownTrait))->toEqual([]);
});
