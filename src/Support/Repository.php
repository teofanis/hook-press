<?php

declare(strict_types=1);

namespace HookPress\Support;

use Illuminate\Filesystem\Filesystem;

class Repository
{
    protected const DEFAULT_FILE_CACHE_PATH = 'bootstrap/cache/hook-press.php';

    public function __construct(
        protected Filesystem $files,
        protected array $config
    ) {}

    public function put(array $map): void
    {
        $driver = data_get($this->config, 'driver', 'file');

        if ($driver === 'cache') {
            $store = data_get($this->config, 'cache.store');
            $key = data_get($this->config, 'cache.key', 'hook-press.:map');
            $ttl = data_get($this->config, 'cache.ttl');

            $cache = cache()->store($store);
            $ttl ? $cache->put($key, $map, $ttl) : $cache->forever($key, $map);

            return;
        }

        // file driver
        $relative = data_get($this->config, 'file.path', static::DEFAULT_FILE_CACHE_PATH);
        $path = base_path($relative);

        $this->files->ensureDirectoryExists(dirname($path));

        $tmp = $path.'.tmp';
        $contents = '<?php return '.var_export($map, true).';';

        $this->files->put($tmp, $contents, true);
        $this->files->move($tmp, $path);
    }

    public function get(): array
    {
        $driver = data_get($this->config, 'driver', 'file');

        if ($driver === 'cache') {
            $store = data_get($this->config, 'cache.store');
            $key = data_get($this->config, 'cache.key', 'hook-press.:map');

            return (array) cache()->store($store)->get($key, []);
        }

        $relative = data_get($this->config, 'file.path', static::DEFAULT_FILE_CACHE_PATH);
        $path = base_path($relative);

        return file_exists($path) ? (array) require $path : [];
    }

    public function clear(): void
    {
        $driver = data_get($this->config, 'driver', 'file');

        if ($driver === 'cache') {
            $store = data_get($this->config, 'cache.store');
            $key = data_get($this->config, 'cache.key', 'hook-press.:map');
            cache()->store($store)->forget($key);

            return;
        }

        $relative = data_get($this->config, 'file.path', static::DEFAULT_FILE_CACHE_PATH);
        $path = base_path($relative);

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
