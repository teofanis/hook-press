<?php

declare(strict_types=1);

use HookPress\Support\Inspector;

beforeEach(function (): void {
    $this->writeClassmap();
});

it('reflects existing classes safely', function (): void {
    $inspector = app(Inspector::class);

    $ref = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);
    expect($ref)->not()->toBeNull()
        ->and($ref->isInstantiable())->toBeTrue();
});

it('returns null for non-existing classes', function (): void {
    $inspector = app(Inspector::class);

    $ref = $inspector->reflect('App\\Nope\\MissingClass');
    expect($ref)->toBeNull();
});

it('detects traits', function (): void {
    $inspector = app(Inspector::class);

    expect($inspector->isTrait(\App\Traits\Searchable::class))->toBeTrue();
});

it('detects trait via isTrait even when not preloaded', function (): void {
    $this->writeClassmap();

    $inspector = app(\HookPress\Support\Inspector::class);

    expect($inspector->isTrait(\App\Traits\Searchable::class))->toBeTrue();
});
