<?php

declare(strict_types=1);

if (! function_exists('array_any')) {
    /**
     * @template T
     *
     * @param  array<T>  $array
     */
    function array_any(array $array, callable $callback): bool
    {
        foreach ($array as $value) {
            if ($callback($value)) {
                return true;
            }
        }

        return false;
    }
}
