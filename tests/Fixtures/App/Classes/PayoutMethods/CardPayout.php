<?php

declare(strict_types=1);

namespace App\Classes\PayoutMethods;

use App\Interfaces\PayoutMethod;
use App\Traits\Searchable;

class CardPayout implements PayoutMethod
{
    use Searchable;

    public function process(float $amount): bool
    {
        return $amount > 0;
    }
}
