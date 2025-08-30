<?php

declare(strict_types=1);

beforeEach(fn () => $this->writeClassmap());

it('build command creates cache file and shows summary', function (): void {
    $this->artisan('hook-press:build')
        ->expectsOutputToContain('HookPress map built.')
        ->assertOk();

    expect(file_exists(base_path('bootstrap/cache/hook-press.php')))->toBeTrue();

    $this->artisan('hook-press:show')
        ->expectsOutputToContain('payout_methods')
        ->assertOk();

    $this->artisan('hook-press:clear')
        ->expectsOutputToContain('HookPress cache cleared.')
        ->assertOk();

    expect(file_exists(base_path('bootstrap/cache/hook-press.php')))->toBeFalse();
});

it('show command can output a single map type', function (): void {
    $this->artisan('hook-press:build')->assertOk();
    $this->artisan('hook-press:show payout_methods')
        ->expectsOutputToContain(\App\Classes\PayoutMethods\CardPayout::class)
        ->assertOk();
});
