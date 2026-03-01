#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * TypeScript Type Generator
 * 
 * Auto-generates TypeScript interfaces from PHP Domain entities and DTOs.
 * 
 * Usage: php scripts/generate-types.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Domain\Model\Article;
use Domain\Model\Page;
use Domain\Model\Form;
use Domain\Model\FormSubmission;
use Domain\Model\User;
use Domain\Model\Contact;
use Domain\Model\Media;
use Domain\ValueObjects\ArticleStatus;
use Domain\ValueObjects\Role;
use Domain\ValueObjects\Slug;
use Domain\ValueObjects\Email;

// Classes to generate types for
$classes = [
    // Entities
    Article::class,
    Page::class,
    Form::class,
    FormSubmission::class,
    User::class,
    Contact::class,
    Media::class,

    // Value Objects (as union types)
    ArticleStatus::class,
    Role::class,
    Slug::class,
    Email::class,
];

$outputDir = __DIR__ . '/../src/Templates/src/types/generated';

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Created output directory: {$outputDir}\n";
}

echo "Generating TypeScript types...\n\n";

$generatedFiles = [];

foreach ($classes as $class) {
    if (!class_exists($class) && !enum_exists($class)) {
        echo "⚠️  Skipping {$class}: class not found\n";
        continue;
    }
    
    $reflection = new ReflectionClass($class);
    $shortName = $reflection->getShortName();
    
    // Check if it's an enum (PHP 8.1+)
    if ($reflection->isEnum()) {
        $typescript = generateEnumType($reflection);
        $filename = "{$shortName}.ts";
    } else {
        $typescript = generateTypeScriptInterface($reflection);
        $filename = "{$shortName}.ts";
    }
    
    $filePath = $outputDir . '/' . $filename;
    file_put_contents($filePath, $typescript);
    $generatedFiles[] = $filename;
    
    echo "✅ Generated: {$filename}\n";
}

// Generate barrel export file
$barrelExport = generateBarrelExport($generatedFiles);
file_put_contents($outputDir . '/index.ts', $barrelExport);
echo "✅ Generated: index.ts (barrel export)\n\n";

echo "✨ Done! Generated " . count($generatedFiles) . " type files in {$outputDir}\n";

/**
 * Generate TypeScript interface from PHP class
 */
function generateTypeScriptInterface(ReflectionClass $reflection): string
{
    $shortName = $reflection->getShortName();
    $namespace = $reflection->getNamespaceName();
    
    $header = <<<TS
/**
 * {$shortName}
 * Auto-generated from PHP class: {$namespace}\\{$shortName}
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */

TS;

    $interface = "export interface {$shortName} {\n";
    
    // Get constructor parameters for type information
    $constructor = $reflection->getConstructor();
    $properties = $reflection->getProperties();
    
    // Filter to only get properties declared in this class (not inherited)
    $classProperties = array_filter($properties, fn($p) => $p->getDeclaringClass()->getName() === $reflection->getName());
    
    if (empty($classProperties)) {
        $interface .= "  // No properties\n";
    } else {
        // Use properties directly
        foreach ($classProperties as $property) {
            // Skip private event properties
            if ($property->getName() === 'events') {
                continue;
            }
            
            $type = mapPhpTypeToTs($property->getType(), $property->getName());
            $name = $property->getName();
            $optional = $property->getType()->allowsNull() ? '?' : '';
            $interface .= "  {$name}{$optional}: {$type};\n";
        }
    }
    
    $interface .= "}\n";
    
    // Generate type guard function
    $interface .= "\n" . generateTypeGuard($shortName, $classProperties);
    
    return $header . $interface;
}

/**
 * Generate TypeScript union type from PHP Enum
 */
function generateEnumType(ReflectionClass $reflection): string
{
    $shortName = $reflection->getShortName();
    $namespace = $reflection->getNamespaceName();
    
    $header = <<<TS
/**
 * {$shortName}
 * Auto-generated from PHP enum: {$namespace}\\{$shortName}
 * ⚠️  DO NOT EDIT - This file is auto-generated
 */

TS;

    // Get enum cases - convert CAMEL_CASE to lowercase
    $cases = $reflection->getConstants();
    $values = array_map('strtolower', array_keys($cases));

    $unionType = implode(' | ', array_map(fn($v) => "'{$v}'", $values));
    
    $typeAlias = "export type {$shortName} = {$unionType};\n\n";
    
    // Generate type guard
    $typeAlias .= "export function is{$shortName}(value: unknown): value is {$shortName} {\n";
    $typeAlias .= "  return typeof value === 'string' && [";
    $typeAlias .= implode(', ', array_map(fn($v) => "'{$v}'", $values));
    $typeAlias .= "].includes(value);\n";
    $typeAlias .= "}\n";
    
    return $header . $typeAlias;
}

/**
 * Generate type guard function
 */
function generateTypeGuard(string $interfaceName, array $properties): string
{
    $guard = "export function is{$interfaceName}(data: unknown): data is {$interfaceName} {\n";
    $guard .= "  return (\n";
    $guard .= "    typeof data === 'object' &&\n";
    $guard .= "    data !== null";
    
    foreach ($properties as $property) {
        if ($property->getName() === 'events') {
            continue;
        }
        $name = $property->getName();
        $type = $property->getType();
        $tsType = getTsTypeForGuard($type);
        $guard .= " &&\n    typeof (data as {$interfaceName}).{$name} === '{$tsType}'";
    }
    
    $guard .= "\n  );\n";
    $guard .= "}\n";
    
    return $guard;
}

/**
 * Map PHP type to TypeScript type
 */
function mapPhpTypeToTs(?ReflectionType $type, string $paramName): string
{
    if ($type === null) {
        return 'unknown';
    }
    
    if ($type instanceof ReflectionUnionType) {
        $types = array_map(fn($t) => mapPhpTypeToTs($t, $paramName), $type->getTypes());
        return implode(' | ', $types);
    }
    
    $typeName = $type->getName();
    
    // Handle nullable types
    $isNullable = $type->allowsNull();
    $nullSuffix = $isNullable ? ' | null' : '';
    
    // Map PHP types to TypeScript
    return match ($typeName) {
        'string' => 'string' . $nullSuffix,
        'int' => 'number' . $nullSuffix,
        'float' => 'number' . $nullSuffix,
        'bool' => 'boolean' . $nullSuffix,
        
        // Handle arrays - check property name for better typing
        'array' => handleArrayType($paramName, $isNullable),
        'iterable' => 'unknown[]' . $nullSuffix,
        'object' => 'Record<string, unknown>' . $nullSuffix,
        'mixed' => 'unknown' . $nullSuffix,
        'void' => 'void' . $nullSuffix,
        'never' => 'never' . $nullSuffix,
        
        // DateTime → ISO 8601 string
        'DateTime', 'DateTimeImmutable', 'DateTimeInterface' => 'string' . $nullSuffix,
        
        // Value Objects - map to primitive types
        'Domain\ValueObjects\Slug' => 'string' . $nullSuffix,
        'Domain\ValueObjects\Email' => 'string' . $nullSuffix,
        'Domain\ValueObjects\Content' => 'string' . $nullSuffix,
        'Domain\ValueObjects\ArticleStatus' => "'draft' | 'published' | 'archived'" . $nullSuffix,
        
        // Default: extract short name
        default => handleCustomType($typeName, $isNullable),
    };
}

/**
 * Handle array type with better naming based on property name
 */
function handleArrayType(string $paramName, bool $isNullable): string
{
    // Special cases based on property name
    $arrayTypeMap = [
        'tags' => 'string[]',
        'slugs' => 'string[]',
        'emails' => 'string[]',
        'ids' => 'string[]',
        'titles' => 'string[]',
    ];
    
    $baseType = $arrayTypeMap[$paramName] ?? 'unknown[]';
    return $baseType . ($isNullable ? ' | null' : '');
}

/**
 * Handle custom PHP types (classes, value objects, etc.)
 */
function handleCustomType(string $typeName, bool $isNullable): string
{
    // Map common value objects to primitive types
    $valueObjectMap = [
        'Domain\\ValueObjects\\ArticleStatus' => "'draft' | 'published' | 'archived'",
        'Domain\\ValueObjects\\Slug' => 'string',
        'Domain\\ValueObjects\\Email' => 'string',
        'Domain\\ValueObjects\\Content' => 'string',
    ];
    
    if (isset($valueObjectMap[$typeName])) {
        return $valueObjectMap[$typeName] . ($isNullable ? ' | null' : '');
    }
    
    // Extract short name for class references
    $parts = explode('\\', $typeName);
    $shortName = end($parts);
    
    // Return as type reference
    return $shortName . ($isNullable ? ' | null' : '');
}

/**
 * Get TypeScript type for type guard
 */
function getTsTypeForGuard(?ReflectionType $type): string
{
    if ($type === null) {
        return 'object';
    }
    
    $typeName = $type->getName();
    
    return match ($typeName) {
        'string' => 'string',
        'int', 'float' => 'number',
        'bool' => 'boolean',
        'array' => 'object',
        default => 'object',
    };
}

/**
 * Generate barrel export file
 */
function generateBarrelExport(array $files): string
{
    $exports = "// Auto-generated barrel export\n";
    $exports .= "// ⚠️  DO NOT EDIT\n\n";
    
    foreach ($files as $file) {
        $name = pathinfo($file, PATHINFO_FILENAME);
        if ($name !== 'index') {
            $exports .= "export type { $name } from './{$name}';\n";
            $exports .= "export { is{$name} } from './{$name}';\n";
        }
    }
    
    return $exports;
}
