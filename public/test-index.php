<?php
declare(strict_types=1);

/**
 * DEBUG TEST FILE - Delete after testing
 * 
 * This file helps diagnose why pip.responsive.sk shows infinite redirect loop
 */

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Debug Test</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1a1a2e;color:#eee}";
echo ".ok{color:#4ade80}.err{color:#f87171}.warn{color:#fbbf24}</style></head><body>";

echo "<h1>🔍 Debug Test - pip.responsive.sk</h1>";
echo "<hr>";

// 1. PHP Info
echo "<h2>1. PHP Environment</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";
echo "<p><strong>Script File:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
echo "<p><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) ? 'ON' : 'OFF') . "</p>";

// 2. File existence checks
echo "<h2>2. File Existence Checks</h2>";

$files = [
    'index.php' => __DIR__ . '/index.php',
    'init.php' => __DIR__ . '/../src/init.php',
    'Kernel.php' => __DIR__ . '/../src/interfaces/HTTP/Kernel.php',
    'composer.json' => __DIR__ . '/../composer.json',
    'vendor/autoload.php' => __DIR__ . '/../vendor/autoload.php',
    '.env' => __DIR__ . '/../.env',
    '.htaccess' => __DIR__ . '/.htaccess',
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "<p class='ok'>✅ $name exists</p>";
    } else {
        echo "<p class='err'>❌ $name NOT FOUND at: $path</p>";
    }
}

// 3. Directory checks
echo "<h2>3. Directory Checks</h2>";

$dirs = [
    'src/' => __DIR__ . '/../src/',
    'vendor/' => __DIR__ . '/../vendor/',
    'storage/' => __DIR__ . '/../storage/',
];

foreach ($dirs as $name => $path) {
    if (is_dir($path)) {
        echo "<p class='ok'>✅ $name directory exists</p>";
    } else {
        echo "<p class='err'>❌ $name directory NOT FOUND</p>";
    }
}

// 4. Try to include init.php
echo "<h2>4. Bootstrap Test</h2>";

$initPath = __DIR__ . '/../src/init.php';
if (file_exists($initPath)) {
    echo "<p class='ok'>✅ init.php found</p>";
    
    try {
        require_once $initPath;
        echo "<p class='ok'>✅ init.php loaded successfully</p>";
    } catch (Throwable $e) {
        echo "<p class='err'>❌ init.php failed to load</p>";
        echo "<p class='err'><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p class='err'><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
        echo "<pre style='background:#2a2a3e;padding:10px;overflow:auto;'>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p class='err'>❌ Cannot test bootstrap - init.php not found</p>";
}

// 5. Test ArticleRepository
echo "<h2>5. ArticleRepository Test</h2>";

if (class_exists('Infrastructure\Persistence\Repositories\ArticleRepository')) {
    echo "<p class='ok'>✅ ArticleRepository class exists</p>";
    
    $reflection = new ReflectionClass('Infrastructure\Persistence\Repositories\ArticleRepository');
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    
    echo "<p><strong>Public methods:</strong></p><ul>";
    foreach ($methods as $method) {
        if (str_starts_with($method->getName(), 'find')) {
            $params = [];
            foreach ($method->getParameters() as $param) {
                $type = $param->getType();
                $typeName = $type ? $type->getName() : 'mixed';
                $default = $param->isDefaultValueAvailable() ? ' = ' . var_export($param->getDefaultValue(), true) : '';
                $params[] = "$typeName \${$param->getName()}$default";
            }
            echo "<li><code>{$method->getName()}(" . implode(', ', $params) . ")</code></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p class='err'>❌ ArticleRepository class not found</p>";
}

// 6. Environment variables
echo "<h2>6. Environment Variables</h2>";

$envVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_DATABASE'];
foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    echo "<p><strong>$var:</strong> " . htmlspecialchars($value) . "</p>";
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
echo "<p class='warn'><strong>⚠️ Delete this file after testing!</strong></p>";
echo "</body></html>";
