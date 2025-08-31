<?php

declare(strict_types=1);

namespace HookPress\Support;

use Illuminate\Support\Str;

class Mapper
{
    /**
     * Raw config array from hook-press.php
     */
    public function __construct(
        protected Scanner $scanner,
        protected Inspector $inspector,
        protected ConditionEvaluator $evaluator,
        /** @var array<string,mixed> $config */
        protected array $config
    ) {}

    /**
     * Build full discovery map.
     *
     * @return array<string,mixed>
     */
    public function build(): array
    {
        $map = [];

        $roots = (array) (data_get($this->config, 'roots', ['App\\']));
        $exclusions = (array) (data_get($this->config, 'exclusions', []));

        // Preload candidate classes
        $allRootClasses = array_keys($this->scanner->classesStartingWith($roots));

        // Optional trait discovery
        $traitsCfg = (array) (data_get($this->config, 'traits', []));
        if (($traitsCfg['enabled'] ?? true) === true) {
            $traitNamespaces = (array) (data_get($traitsCfg, 'namespaces', []));
            $groupKey = (string) (data_get($traitsCfg, 'group_key', 'traits'));

            $traitNames = $this->scanner->traitNames($traitNamespaces, $this->inspector);
            foreach ($allRootClasses as $class) {
                if ($this->excluded($class, $exclusions)) {
                    continue;
                }

                $ref = $this->inspector->reflect($class);
                if (! $ref instanceof \ReflectionClass) {
                    continue;
                }
                if (! $ref->isInstantiable()) {
                    continue;
                }

                $classTraits = $ref->getTraitNames();
                foreach ($traitNames as $trait) {
                    if (in_array($trait, $classTraits, true)) {
                        $map[$groupKey][$trait][] = $class;
                    }
                }
            }

            // normalize & sort
            if (isset($map[$groupKey])) {
                foreach ($map[$groupKey] as &$list) {
                    $list = array_values(array_unique($list));
                    sort($list);
                }
                ksort($map[$groupKey]);
            }
        }

        // Maps
        $maps = (array) (data_get($this->config, 'maps', []));

        foreach ($maps as $type => $def) {
            $namespaces = (array) (data_get($def, 'namespaces', []));
            $conditions = (array) (data_get($def, 'conditions', []));

            $candidates = array_keys($this->scanner->classesStartingWith($namespaces));

            $bucket = [];

            foreach ($candidates as $class) {
                if ($this->excluded($class, $exclusions)) {
                    continue;
                }

                $ref = $this->inspector->reflect($class);

                if (! $ref instanceof \ReflectionClass) {
                    continue;
                }
                if ($this->evaluator->passes($ref, $conditions)) {
                    $bucket[] = $class;
                }
            }

            $bucket = array_values(array_unique($bucket));
            sort($bucket);

            $map[$type] = $bucket;
        }

        ksort($map);

        return $map;
    }

    /**
     * Exclusion logic (by exact class, namespace prefix, or regex)
     *
     * @param  array<string,mixed>  $exclusions
     */
    protected function excluded(string $class, array $exclusions): bool
    {
        $classes = (array) data_get($exclusions, 'classes', []);
        if (in_array($class, $classes, true)) {
            return true;
        }

        $namespaces = (array) data_get($exclusions, 'namespaces', []);
        foreach ($namespaces as $ns) {
            if (Str::startsWith($class, $ns)) {
                return true;
            }
        }

        $regexes = (array) data_get($exclusions, 'regex', []);

        return array_any($regexes, fn (string $pattern): bool => @preg_match($pattern, $class) === 1);
    }
}
