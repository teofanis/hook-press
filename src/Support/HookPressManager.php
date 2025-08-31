<?php

declare(strict_types=1);

namespace HookPress\Support;

class HookPressManager
{
    public function __construct(
        protected Repository $repository,
        protected Mapper $mapper,
    ) {}

    /**
     * Get the entire map or single map for a given type.
     *
     * @return array<string,mixed>
     */
    public function map(?string $type = null): array
    {
        $map = $this->repository->get();

        return is_null($type) ? $map : data_get($map, $type, []);
    }

    /**
     * Get the classes that use a given trait.
     *
     * @return array<string,mixed>
     */
    public function classesUsing(string $trait): array
    {
        $map = $this->repository->get();
        $groupKey = config('hook-press.traits.group_key', 'traits');

        return data_get($map, "{$groupKey}.{$trait}", []);
    }

    /**
     * Build and persist the map.
     *
     * @return array<string,mixed>
     */
    public function refresh(): array
    {
        $map = $this->mapper->build();
        $this->repository->put($map);

        return $map;
    }

    /**
     * Clear the map.
     */
    public function clear(): void
    {
        $this->repository->clear();
    }
}
