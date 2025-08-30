<?php

declare(strict_types=1);

namespace HookPress\HookPress\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HookPress\HookPress\HookPress
 */
class HookPress extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HookPress\HookPress\HookPress::class;
    }
}
