<?php

declare(strict_types=1);

use HookPress\Support\Repository;
use Illuminate\Filesystem\Filesystem;

it('writes, reads, and clears the cache repository', function (): void {
    config()->set('cache.default', 'array');
    config()->set('hook-press.store.driver', 'cache');
    config()->set('hook-press.store.cache.store', 'array');
    config()->set('hook-press.store.cache.key', 'hook-press.:test');

    $repo = new Repository(app(Filesystem::class), config('hook-press.store'));

    $map = ['alpha' => ['A', 'B']];

    $repo->put($map);
    expect($repo->get())->toEqual($map);

    $repo->clear();
    expect($repo->get())->toEqual([]);
});
