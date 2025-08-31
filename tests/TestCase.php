<?php

declare(strict_types=1);

namespace HookPress\Tests;

use HookPress\HookPressServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
            HookPressServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('hook-press', [
            'roots' => ['App\\'],
            'traits' => [
                'enabled' => true,
                'namespaces' => ['App\\Traits\\'],
                'group_key' => 'traits',
            ],
            'maps' => [
                'payout_methods' => [
                    'namespaces' => ['App\\Classes\\PayoutMethods\\'],
                    'conditions' => [
                        'isInstantiable',
                        'implementsInterface' => \App\Interfaces\PayoutMethod::class,
                    ],
                ],
                'attributed' => [
                    'namespaces' => ['App\\Classes\\Attributed\\'],
                    'conditions' => [
                        'hasAttribute' => \App\Attributes\Discoverable::class,
                    ],
                ],
            ],
            'exclusions' => [
                'classes' => [],
                'namespaces' => [],
                'regex' => [],
            ],
            'store' => [
                'driver' => 'file',
                'file' => ['path' => 'bootstrap/cache/hook-press.php'],
                'cache' => ['store' => 'array', 'key' => 'hook-press.:map', 'ttl' => null],
            ],
            'composer' => [
                'artisan_command' => 'hook-press.:build',
                'classmap_path' => 'tests/.tmp/autoload_classmap.php',
            ],
        ]);

        // Ensure vendor/composer dir exists for the fake classmap
        (new Filesystem())->ensureDirectoryExists(base_path('vendor/composer'));
    }

    /**
     * Fake Composer classmap pointing to our fixture classes.
     */
    protected function writeClassmap(): void
    {
        $fs = new Filesystem();

        $fakeDir = base_path(str(config('hook-press.composer.classmap_path', 'tests/.tmp/autoload_classmap.php'))->beforeLast('/'));
        $fs->ensureDirectoryExists($fakeDir);

        $fakePath = $fakeDir.'/autoload_classmap.php';

        $map = [
            \App\Traits\Searchable::class => realpath(__DIR__.'/Fixtures/App/Traits/Searchable.php'),
            \App\Interfaces\PayoutMethod::class => realpath(__DIR__.'/Fixtures/App/Interfaces/PayoutMethod.php'),
            \App\Attributes\Discoverable::class => realpath(__DIR__.'/Fixtures/App/Attributes/Discoverable.php'),
            \App\Classes\PayoutMethods\CardPayout::class => realpath(__DIR__.'/Fixtures/App/Classes/PayoutMethods/CardPayout.php'),
            \App\Classes\PayoutMethods\BankPayout::class => realpath(__DIR__.'/Fixtures/App/Classes/PayoutMethods/BankPayout.php'),
            \App\Classes\PayoutMethods\AbstractBase::class => realpath(__DIR__.'/Fixtures/App/Classes/PayoutMethods/AbstractBase.php'),
            \App\Classes\Other\Unrelated::class => realpath(__DIR__.'/Fixtures/App/Classes/Other/Unrelated.php'),
            \App\Classes\Attributed\Marked::class => realpath(__DIR__.'/Fixtures/App/Classes/Attributed/Marked.php'),
            \App\Classes\PayoutMethods\ChildOfAbstract::class => realpath(__DIR__.'/Fixtures/App/Classes/PayoutMethods/ChildOfAbstract.php'),
            \App\Classes\Finals\FinalService::class => realpath(__DIR__.'/Fixtures/App/Classes/Finals/FinalService.php'),
            \App\Classes\WithProps\DriverService::class => realpath(__DIR__.'/Fixtures/App/Classes/WithProps/DriverService.php'),
        ];

        expect($map)->toBeValidAutoloadClassmap();

        $contents = "<?php\nreturn ".var_export($map, true).";\n";
        $fs->put($fakePath, $contents);
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        $this->defineEnvironment($app);
    }
}
