<?php

if (!function_exists('array_any')) {
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
