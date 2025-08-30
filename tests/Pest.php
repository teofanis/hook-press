<?php

declare(strict_types=1);

use HookPress\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Since we are creating a dummy autoload_classmap.php for our fixtures,
 * We'll also register a tiny autoload to resolve them, as the orchestra autoload won't
 * pick them up.
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $base = __DIR__.'/Fixtures/App/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $base.str_replace('\\', '/', $relative).'.php';

    if (is_file($file)) {
        require $file;
    }
});

expect()->extend('toBeValidAutoloadClassmap', function (): \Pest\Expectation {
    if (! is_array($this->value)) {
        test()->fail('passed value is expected to be an array.');
    }
    $countMap = count($this->value);
    $countValidGenerated = count(
        array_filter($this->value, fn ($v): bool => (bool) $v)
    );

    if ($countMap !== $countValidGenerated) {
        $path = base_path(str(config('hook-press.composer.classmap_path', 'tests/.tmp/autoload_classmap.php')));
        test()->fail("Your fixture autoload_classmap.php probably has a typo as it contains invalid paths. Check your config and the generated file: {$path}");
    }

    return expect($countMap)->toBe($countValidGenerated);
});
