<?php

declare(strict_types=1);

namespace HookPress;

use HookPress\Commands\BuildCommand;
use HookPress\Commands\ClearCommand;
use HookPress\Commands\ShowCommand;
use HookPress\Conditions\HasAttribute;
use HookPress\Conditions\ImplementsInterface;
use HookPress\Conditions\IsInstantiable;
use HookPress\Conditions\UsesTrait;
use HookPress\Support\ConditionEvaluator;
use HookPress\Support\HookPressManager;
use HookPress\Support\Inspector;
use HookPress\Support\Mapper;
use HookPress\Support\Repository;
use HookPress\Support\Scanner;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BuildCommand::class,
                ClearCommand::class,
                ShowCommand::class,
            ]);
        }
    }

    public function packageRegistered(): void
    {
        require_once __DIR__.'/polyfills.php';

        $this->app->singleton(Repository::class, fn (Application $app): \HookPress\Support\Repository => new Repository($app->make(Filesystem::class), $app['config']->get('hook-press.store')));
        $this->app->singleton(Inspector::class, fn (): \HookPress\Support\Inspector => new Inspector);

        $this->app->singleton(Scanner::class, fn (Application $app): \HookPress\Support\Scanner => new Scanner($app['config']->get('hook-press.roots')));

        $this->app->singleton(ConditionEvaluator::class, function (Application $app): \HookPress\Support\ConditionEvaluator {
            // Built-in conditions registry
            $builtIns = [
                'isInstantiable' => new IsInstantiable,
                'implementsInterface' => new ImplementsInterface,
                'usesTrait' => new UsesTrait,
                'hasAttribute' => new HasAttribute,
            ];

            return new ConditionEvaluator($app, $builtIns);
        });

        $this->app->singleton(Mapper::class, fn (Application $app): \HookPress\Support\Mapper => new Mapper(
            $app->make(Scanner::class),
            $app->make(Inspector::class),
            $app->make(ConditionEvaluator::class),
            $app['config']->get('hook-press')
        ));

        $this->app->singleton(HookPressManager::class, fn ($app): \HookPress\Support\HookPressManager => new HookPressManager(
            $app->make(Repository::class),
            $app->make(Mapper::class)
        ));
    }
}
