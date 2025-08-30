<?php

namespace HookPress\HookPress\Commands;

use Illuminate\Console\Command;

class HookPressCommand extends Command
{
    public $signature = 'hook-press';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
