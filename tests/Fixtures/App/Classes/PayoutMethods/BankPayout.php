<?php

declare(strict_types=1);

namespace App\Classes\PayoutMethods;

use App\Interfaces\PayoutMethod;

class BankPayout implements PayoutMethod
{
    public function process(float $amount): bool
    {
        return $amount > 10;
    }
}
