<?php

declare(strict_types=1);

namespace HookPress\Support;

use Illuminate\Support\Str;

class Scanner
{
    protected const COMPOSER_CLASS_MAP = '/vendor/composer/autoload_classmap.php';

    /**
     * @param  array<int,string>  $roots
     */
    public function __construct(protected array $roots = ['App\\']) {}

    /**
     * Load Composer's class map.
     *
     * @return array<string,string> [class => file]
     */
    public function classMap(): array
    {
        $basePath = function_exists('base_path') ? base_path() : getcwd();
        $configPath = config('hook-press.composer.classmap_path', static::COMPOSER_CLASS_MAP);
        if (! is_string($configPath)) {
            $configPath = static::COMPOSER_CLASS_MAP;
        }
        $relative = ltrim($configPath, DIRECTORY_SEPARATOR);
        $path = $basePath.DIRECTORY_SEPARATOR.$relative;

        if (! file_exists($path)) {
            return [];
        }

        /** @var array<string,string> $map */
        $map = require $path;

        return is_array($map) ? $map : [];
    }

    /**
     * Filter classes by namespace prefixes
     *
     * @param  array<int,string>  $prefixes
     * @return array<string,string> [class => file]
     */
    public function classesStartingWith(array $prefixes): array
    {
        $map = $this->classMap();

        if ($prefixes === []) {
            return $map;
        }

        $filtered = [];

        foreach ($map as $class => $file) {
            foreach ($prefixes as $prefix) {
                if (Str::startsWith($class, $prefix)) {
                    $filtered[$class] = $file;
                    break;
                }
            }
        }

        return $filtered;
    }

    /**
     * Traits within the provided namespaces.
     *
     * @param  array<int,string>  $traitNamespaces
     * @return array<int,string> trait FQCNs
     */
    public function traitNames(array $traitNamespaces, Inspector $inspector): array
    {
        if ($traitNamespaces === []) {
            return [];
        }

        $candidates = $this->classesStartingWith($traitNamespaces);
        $traits = [];

        foreach (array_keys($candidates) as $class) {
            if ($inspector->isTrait($class)) {
                $traits[] = $class;
            }
        }

        sort($traits);

        return $traits;
    }
}
