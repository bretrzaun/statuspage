<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withPhpSets(php81: true)
    ->withSets([
        PHPUnitSetList::PHPUNIT_100
    ])
    ->withTypeCoverageLevel(0);
