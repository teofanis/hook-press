<?php

declare(strict_types=1);

use HookPress\Contracts\Condition;
use HookPress\Hooks\HookRunner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Facade;

arch()->preset()->security()
    ->ignoring(HookRunner::class);
// arch()->preset()->php()->ignore('var_export'); TODO
arch()->preset()->laravel();
arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch()
    ->expect('HookPress')
    ->toUseStrictTypes();
arch()
    ->expect('HookPress\Commands')
    ->toBeClasses()
    ->toExtend(Command::class)
    ->toHaveSuffix('Command');

arch()
    ->expect('HookPress\Contracts')
    ->toBeInterfaces();
arch()
    ->expect('HookPress\Facades')
    ->toExtend(Facade::class);
arch()
    ->expect('HookPress\Conditions')
    ->toBeClasses()
    ->toImplement(Condition::class);

arch()
    ->expect('HookPress\Support')
    ->toBeClasses();
