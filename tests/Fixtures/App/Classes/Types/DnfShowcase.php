<?php

declare(strict_types=1);

namespace App\Classes\Types;

/**
 * Disjunctive Normal Form types are PHP 8.2+, so this lives in its own file:
 * the test that references it is skipped below 8.2 and the file is then never
 * autoloaded, let alone parsed.
 */
class DnfShowcase
{
    public function dnf(): (Alpha&Beta)|string
    {
        return '';
    }
}
