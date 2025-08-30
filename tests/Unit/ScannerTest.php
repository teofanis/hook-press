<?php

declare(strict_types=1);

use HookPress\Support\Inspector;
use HookPress\Support\Scanner;

beforeEach(fn () => $this->writeClassmap());

it('loads composer classmap', function (): void {
    $scanner = app(Scanner::class);
    $map = $scanner->classMap();

    expect($map)->toBeArray()
        ->and($map)->toHaveKey(\App\Classes\PayoutMethods\CardPayout::class);
});

it('filters by namespace prefixes', function (): void {
    $scanner = app(Scanner::class);

    $filtered = $scanner->classesStartingWith(['App\\Classes\\PayoutMethods\\']);

    expect($filtered)->toHaveKey(\App\Classes\PayoutMethods\CardPayout::class)
        ->and($filtered)->not()->toHaveKey(\App\Classes\Other\Unrelated::class);
});

it('identifies traits within namespaces', function (): void {
    $scanner = app(Scanner::class);
    $inspector = app(Inspector::class);

    $traits = $scanner->traitNames(['App\\Traits\\'], $inspector);

    expect($traits)->toContain(\App\Traits\Searchable::class);
});

it('returns full map when no prefixes provided', function (): void {
    $this->writeClassmap();
    $scanner = app(\HookPress\Support\Scanner::class);

    $all = $scanner->classMap();
    $filtered = $scanner->classesStartingWith([]);

    expect($filtered)->toEqual($all);
});
