<?php

declare(strict_types=1);

namespace HookPress;

use HookPress\Commands\BuildCommand;
use HookPress\Commands\ClearCommand;
use HookPress\Commands\ShowCommand;
use HookPress\Conditions\ExtendsClass;
use HookPress\Conditions\HasAttribute;
use HookPress\Conditions\HasMethod;
use HookPress\Conditions\HasProperty;
use HookPress\Conditions\ImplementsInterface;
use HookPress\Conditions\IsAbstract;
use HookPress\Conditions\IsFinal;
use HookPress\Conditions\IsInstantiable;
use HookPress\Conditions\NameMatches;
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
        $this->loadPolyfills();
        $this->configureBindings();
    }

    private function configureBindings(): void
    {
        $this->app->singleton(Repository::class, function (Application $app): Repository {
            /** @var array<string, mixed> $config */
            $config = $app['config']->get('hook-press.store', []);

            return new Repository($app->make(Filesystem::class), $config);
        });

        $this->app->singleton(Inspector::class, fn (): Inspector => new Inspector);

        $this->app->singleton(Scanner::class, function (Application $app): Scanner {
            /** @var array<int, string> $roots */
            $roots = $app['config']->get('hook-press.roots', ['App\\']);

            return new Scanner($roots);
        });

        $this->app->singleton(ConditionEvaluator::class, function (Application $app): ConditionEvaluator {
            $builtIns = $this->getBuiltIns();

            return new ConditionEvaluator($app, $builtIns);
        });

        $this->app->singleton(Mapper::class, function (Application $app): Mapper {
            /** @var array<string, mixed> $config */
            $config = $app['config']->get('hook-press', []);

            return new Mapper(
                $app->make(Scanner::class),
                $app->make(Inspector::class),
                $app->make(ConditionEvaluator::class),
                $config
            );
        });

        $this->app->singleton(HookPressManager::class, fn (Application $app): HookPressManager => new HookPressManager(
            $app->make(Repository::class),
            $app->make(Mapper::class)
        ));
    }

    private function loadPolyfills(): void
    {
        require_once __DIR__.'/polyfills.php';
    }

    /**
     * @return array<string, \HookPress\Contracts\Condition>
     */
    private function getBuiltIns(): array
    {
        return [
            'isInstantiable' => new IsInstantiable,
            'implementsInterface' => new ImplementsInterface,
            'usesTrait' => new UsesTrait,
            'hasAttribute' => new HasAttribute,
            'extends' => new ExtendsClass,
            'isAbstract' => new IsAbstract,
            'isFinal' => new IsFinal,
            'hasMethod' => new HasMethod,
            'hasProperty' => new HasProperty,
            'nameMatches' => new NameMatches,
        ];
    }
}
