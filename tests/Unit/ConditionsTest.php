<?php

declare(strict_types=1);

use HookPress\Conditions\HasAttribute;
use HookPress\Conditions\ImplementsInterface;
use HookPress\Conditions\IsInstantiable;
use HookPress\Conditions\UsesTrait;
use HookPress\Support\Inspector;

beforeEach(fn () => $this->writeClassmap());

it('checks IsInstantiable', function (): void {
    $inspector = app(Inspector::class);
    $is = new IsInstantiable;

    $ref1 = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);
    $ref2 = $inspector->reflect(\App\Classes\PayoutMethods\AbstractBase::class);

    expect($is->passes($ref1))->toBeTrue()
        ->and($is->passes($ref2))->toBeFalse();
});

it('checks ImplementsInterface', function (): void {
    $inspector = app(Inspector::class);
    $cond = new ImplementsInterface;

    $ref1 = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);
    $ref2 = $inspector->reflect(\App\Classes\Other\Unrelated::class);

    expect($cond->passes($ref1, \App\Interfaces\PayoutMethod::class))->toBeTrue()
        ->and($cond->passes($ref2, \App\Interfaces\PayoutMethod::class))->toBeFalse();
});

it('checks UsesTrait', function (): void {
    $inspector = app(Inspector::class);
    $cond = new UsesTrait;

    $ref1 = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);
    $ref2 = $inspector->reflect(\App\Classes\PayoutMethods\BankPayout::class);

    expect($cond->passes($ref1, \App\Traits\Searchable::class))->toBeTrue()
        ->and($cond->passes($ref2, \App\Traits\Searchable::class))->toBeFalse();
});

it('checks HasAttribute', function (): void {
    $inspector = app(Inspector::class);
    $cond = new HasAttribute;

    $ref1 = $inspector->reflect(\App\Classes\Attributed\Marked::class);
    $ref2 = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    expect($cond->passes($ref1, \App\Attributes\Discoverable::class))->toBeTrue()
        ->and($cond->passes($ref2, \App\Attributes\Discoverable::class))->toBeFalse();
});

it('guards against invalid condition arguments', function (): void {
    $inspector = app(\HookPress\Support\Inspector::class);

    $ref = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    $impl = new \HookPress\Conditions\ImplementsInterface;
    expect($impl->passes($ref, ''))->toBeFalse();

    $attr = new \HookPress\Conditions\HasAttribute;
    expect($attr->passes($ref, null))->toBeFalse();
});
