<?php

declare(strict_types=1);

namespace HookPress\Commands;

use HookPress\Support\HookPressManager;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $signature = 'hook-press:clear';

    protected $description = 'Clear HookPress cached discovery maps.';

    public function handle(HookPressManager $manager): int
    {
        $manager->clear();

        $this->components->info('HookPress cache cleared.');

        return self::SUCCESS;
    }
}
