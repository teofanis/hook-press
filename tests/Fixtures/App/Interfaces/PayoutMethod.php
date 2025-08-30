<?php

declare(strict_types=1);

namespace App\Interfaces;

interface PayoutMethod
{
    public function process(float $amount): bool;
}
