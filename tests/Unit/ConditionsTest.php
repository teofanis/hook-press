<?php

declare(strict_types=1);

use HookPress\Conditions\ExtendsClass;
use HookPress\Conditions\HasAttribute;
use HookPress\Conditions\HasMethod;
use HookPress\Conditions\HasProperty;
use HookPress\Conditions\ImplementsInterface;
use HookPress\Conditions\IsAbstract;
use HookPress\Conditions\IsFinal;
use HookPress\Conditions\IsInstantiable;
use HookPress\Conditions\NameMatches;
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

it('checks ExtendsClass', function (): void {
    $inspector = app(Inspector::class);
    $cond = new ExtendsClass;

    $ref1 = $inspector->reflect(\App\Classes\PayoutMethods\ChildOfAbstract::class);
    $ref2 = $inspector->reflect(\App\Classes\Other\Unrelated::class);

    expect($cond->passes($ref1, \App\Classes\PayoutMethods\AbstractBase::class))->toBeTrue()
        ->and($cond->passes($ref2, \App\Classes\PayoutMethods\AbstractBase::class))->toBeFalse();
});

it('checks IsAbstract', function (): void {
    $inspector = app(Inspector::class);
    $cond = new IsAbstract;

    $refAbstract = $inspector->reflect(\App\Classes\PayoutMethods\AbstractBase::class);
    $refConcrete = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    expect($cond->passes($refAbstract))->toBeTrue()
        ->and($cond->passes($refConcrete))->toBeFalse();
});

it('checks IsFinal', function (): void {
    $inspector = app(Inspector::class);
    $cond = new IsFinal;

    $refFinal = $inspector->reflect(\App\Classes\Finals\FinalService::class);
    $refNonFinal = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    expect($cond->passes($refFinal))->toBeTrue()
        ->and($cond->passes($refNonFinal))->toBeFalse();
});

it('checks HasMethod by name and with constraints', function (): void {
    $inspector = app(Inspector::class);
    $cond = new HasMethod;

    $ref = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    // exists by name
    expect($cond->passes($ref, 'process'))->toBeTrue();

    // with constraints: public, non-static, returns bool
    expect($cond->passes($ref, [
        'name' => 'process',
        'public' => true,
        'static' => false,
        'returns' => 'bool',
    ]))->toBeTrue();

    // negative: method does not exist
    expect($cond->passes($ref, 'handle'))->toBeFalse();
});

it('checks HasProperty by name and with constraints', function (): void {
    $inspector = app(Inspector::class);
    $cond = new HasProperty;

    $ref = $inspector->reflect(\App\Classes\WithProps\DriverService::class);

    // exists by name
    expect($cond->passes($ref, 'driver'))->toBeTrue();

    // with constraints: public, non-static, typed string
    expect($cond->passes($ref, [
        'name' => 'driver',
        'public' => true,
        'static' => false,
        'type' => 'string',
    ]))->toBeTrue();

    // negative: wrong type
    expect($cond->passes($ref, [
        'name' => 'driver',
        'type' => 'int',
    ]))->toBeFalse();
});

it('checks NameMatches on FQCN and short name', function (): void {
    $inspector = app(Inspector::class);
    $cond = new NameMatches;

    $refCard = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);
    $refBank = $inspector->reflect(\App\Classes\PayoutMethods\BankPayout::class);

    // FQCN ends with "Payout"
    expect($cond->passes($refCard, '/Payout$/'))->toBeTrue();

    // Short name starts with Bank
    expect($cond->passes($refBank, ['pattern' => '/^Bank/', 'short' => true]))->toBeTrue();

    // Negative: short name does not match
    expect($cond->passes($refCard, ['pattern' => '/^Bank/', 'short' => true]))->toBeFalse();
});
