<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withSets([
        SetList::PHP_84,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SetList::EARLY_RETURN,
    ])
    ->withSkip([
        // Skip for legacy code
        __DIR__ . '/src/Infrastructure/Paths/AppPaths.php',
        __DIR__ . '/src/Infrastructure/Persistence/DatabaseConnection.php',
        
        // Skip specific rules
        \Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector::class,
    ])
    ->withImportNames(
        importShortClasses: true,
        removeUnusedImports: true,
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        naming: true,
        privatization: true,
        typeDeclarations: true,
        earlyReturn: true,
    );
