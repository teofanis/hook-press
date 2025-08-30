<?php

namespace HookPress\HookPress;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use HookPress\HookPress\Commands\HookPressCommand;

class HookPressServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('hook-press')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_hook_press_table')
            ->hasCommand(HookPressCommand::class);
    }
}
