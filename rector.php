<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withPhpSets()
    ->withPreparedSets(deadCode: true)
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withTypeCoverageLevel(4)
;
