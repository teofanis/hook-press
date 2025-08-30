<?php

declare(strict_types=1);

use HookPress\Support\Repository;
use Illuminate\Filesystem\Filesystem;

it('writes, reads, and clears the file repository', function (): void {
    $fs = new Filesystem;

    config()->set('hook-press.store.driver', 'file');
    config()->set('hook-press.store.file.path', 'bootstrap/cache/hook-press.php');

    $repo = new Repository($fs, config('hook-press.store'));

    $map = ['foo' => ['App\\Foo'], 'traits' => [\App\Traits\Searchable::class => ['App\\Bar']]];
    $repo->put($map);

    $path = base_path('bootstrap/cache/hook-press.php');
    expect(file_exists($path))->toBeTrue();

    $loaded = $repo->get();
    expect($loaded)->toEqual($map);

    $repo->clear();
    expect(file_exists($path))->toBeFalse();
});
