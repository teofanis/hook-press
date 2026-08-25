<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Php84\Rector\Foreach_\ForeachToArrayAnyRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__.'/src',
            __DIR__.'/tests',
        ])->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            typeDeclarations: true,
            privatization: true,
            earlyReturn: true,
        )->withPhpSets(php85: true)
        ->withRules([
            DeclareStrictTypesRector::class,
        ])
        ->withSkip([
            // The polyfill *is* the PHP < 8.4 implementation of array_any();
            // rewriting its body to array_any() would make it recurse forever.
            ForeachToArrayAnyRector::class => [
                __DIR__.'/src/polyfills.php',
            ],
        ]);
} catch (InvalidConfigurationException $e) {
    echo $e->getMessage();
    exit(1);
}
