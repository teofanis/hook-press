<?php

declare(strict_types=1);

namespace App\Classes\PayoutMethods;

use App\Interfaces\PayoutMethod;

abstract class AbstractBase implements PayoutMethod
{
    public function process(float $amount): bool
    {
        return $amount > 0;
    }
}
