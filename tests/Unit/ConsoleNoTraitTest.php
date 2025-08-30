<?php

declare(strict_types=1);

it('builds without traits when --no-traits flag is used', function (): void {
    $this->writeClassmap();

    expect(config('hook-press.traits.enabled'))->toBeTrue();

    $this->artisan('hook-press:build --no-traits')->assertOk();

    expect(config('hook-press.traits.enabled'))->toBeTrue();

    $map = require base_path('bootstrap/cache/hook-press.php');

    // traits group should be absent
    $traitsKey = config('hook-press.traits.group_key', 'traits');
    expect(array_key_exists($traitsKey, $map))->toBeFalse();
});
