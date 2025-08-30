<?php

declare(strict_types=1);

namespace HookPress\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HookPress\HookPressManager
 */
class HookPress extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HookPress\Support\HookPressManager::class;
    }
}
