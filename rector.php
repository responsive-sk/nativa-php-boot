<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\Property\AddPropertyTypeDeclarationRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\CodeQuality\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;
use Rector\Php84\Rector\Property\TypedPropertyFromStrictSetUpRector;

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
    ->withRules([
        // Strict types
        \Rector\TypeDeclaration\Rector\Class_\PropertyTypeFromStrictSetterGetterRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictStrictCallRector::class,
        
        // Code Quality
        EmptyOnNullableObjectToInstanceOfRector::class,
        LocallyCalledStaticMethodToNonStaticRector::class,
        
        // Dead Code
        RemoveUnusedConstructorParamRector::class,
        RemoveUnusedPrivateMethodRector::class,
        RemoveUnusedPrivatePropertyRector::class,
    ])
    ->withSkip([
        // Skip for legacy code
        __DIR__ . '/src/infrastructure/Paths/AppPaths.php',
        __DIR__ . '/src/infrastructure/Persistence/DatabaseConnection.php',
        
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
