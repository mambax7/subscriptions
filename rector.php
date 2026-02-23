<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/admin',
        __DIR__ . '/blocks',
        __DIR__ . '/include',
        __DIR__ . '/class',
    ])
    ->withPhpSets(php74: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ])
    ->withCache(__DIR__ . '/.build/rector/')
    ->withSkip([
        // Skip XOOPS legacy files that can't be modernized
        __DIR__ . '/preloads',
    ]);
