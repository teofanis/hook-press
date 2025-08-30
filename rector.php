<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
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
            strictBooleans: true,
        )->withPhpSets(php84: true)
        ->withRules([
            DeclareStrictTypesRector::class,
        ]);
} catch (\Rector\Exception\Configuration\InvalidConfigurationException $e) {
    echo $e->getMessage();
    exit(1);
}
