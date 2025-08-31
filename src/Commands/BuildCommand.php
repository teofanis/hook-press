<?php

declare(strict_types=1);

namespace HookPress\Commands;

use HookPress\Support\HookPressManager;
use HookPress\Support\Mapper;
use HookPress\Support\Repository;
use Illuminate\Console\Command;

class BuildCommand extends Command
{
    protected $signature = 'hook-press:build {--no-traits : Skip trait discovery}';

    protected $description = 'Build and cache HookPress discovery maps.';

    public function handle(HookPressManager $manager): int
    {
        if ($this->option('no-traits')) {
            $previousTraitSetting = config('hook-press.traits.enabled', true);
            // Temporarily disable traits for this run
            config(['hook-press.traits.enabled' => false]);
            $manager = $this->refreshManager();
        }

        $map = $manager->refresh();

        $this->components->info('HookPress map built.');
        $this->line('');

        foreach ($map as $key => $value) {
            if ($key === (config('hook-press.traits.group_key', 'traits'))) {
                $total = array_sum(array_map(fn (array $arr): int => count($arr), $value));
                $this->components->twoColumnDetail("• {$key}", "{$total} classes across ".count($value).' traits');

                continue;
            }

            $this->components->twoColumnDetail("• {$key}", (string) count($value));
        }

        if (isset($previousTraitSetting)) {
            config(['hook-press.traits.enabled' => $previousTraitSetting]);
            $this->refreshManager();
        }

        return self::SUCCESS;
    }

    private function refreshManager(): HookPressManager
    {
        app()->forgetInstance(HookPressManager::class);
        app()->forgetInstance(Mapper::class);

        return app()->make(HookPressManager::class, [
            app()->make(Repository::class),
            app()->make(Mapper::class),
        ]);

    }
}
