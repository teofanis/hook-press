<?php

declare(strict_types=1);

use HookPress\Support\Scanner;

it('returns empty map when classmap path is missing', function (): void {
    // non-existing
    config()->set('hook-press.composer.classmap_path', 'tests/.tmp/missing_autoload_classmap.php');

    /** @var Scanner $scanner */
    $scanner = app(Scanner::class);

    expect($scanner->classMap())->toBeArray()->toBeEmpty();
});
