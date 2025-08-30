<?php

declare(strict_types=1);

namespace App\Classes\WithProps;

class DriverService
{
    public string $driver = 'stripe';

    protected static ?string $cacheStore = null;

    public function handle(): void {}
}
