<?php

declare(strict_types=1);

use HookPress\Support\ConditionEvaluator;
use HookPress\Support\Inspector;

beforeEach(fn () => $this->writeClassmap());

it('evaluates chained conditions', function (): void {
    $evaluator = app(ConditionEvaluator::class);
    $inspector = app(Inspector::class);

    $ref = $inspector->reflect(\App\Classes\PayoutMethods\CardPayout::class);

    $ok = $evaluator->passes($ref, [
        'isInstantiable',
        'implementsInterface' => \App\Interfaces\PayoutMethod::class,
    ]);

    expect($ok)->toBeTrue();
});

it('fails when any condition fails', function (): void {
    $evaluator = app(ConditionEvaluator::class);
    $inspector = app(Inspector::class);

    $ref = $inspector->reflect(\App\Classes\PayoutMethods\BankPayout::class);

    $ok = $evaluator->passes($ref, [
        'isInstantiable',
        'usesTrait' => \App\Traits\Searchable::class,
    ]);

    expect($ok)->toBeFalse();
});
