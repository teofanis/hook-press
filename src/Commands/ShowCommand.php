<?php

declare(strict_types=1);

namespace HookPress\Commands;

use HookPress\Support\HookPressManager;
use Illuminate\Console\Command;

class ShowCommand extends Command
{
    protected $signature = 'hook-press:show {type? : Map type to show}';

    protected $description = 'Show HookPress discovery maps.';

    public function handle(HookPressManager $manager): int
    {
        $type = $this->argument('type');

        $map = $manager->map($type);
        if ($type) {
            if ($map === []) {
                $this->components->warn("No entries for '{$type}'.");

                return self::SUCCESS;
            }

            foreach ($map as $class) {
                $this->line($class);
            }

            return self::SUCCESS;
        }

        if ($map === []) {
            $this->components->warn('No HookPress map found. Run hook-press:build.');

            return self::SUCCESS;
        }

        foreach ($map as $key => $value) {
            $this->newLine();
            $this->components->info($key);

            if ($key === (config('hook-press.traits.group_key', 'traits'))) {
                foreach ($value as $trait => $classes) {
                    $this->line("  {$trait}");
                    foreach ($classes as $class) {
                        $this->line("    - {$class}");
                    }
                }
            } else {
                foreach ($value as $class) {
                    $this->line("  - {$class}");
                }
            }
        }

        return self::SUCCESS;
    }
}
