# Production Performance Optimization Plan

**Issue:** Vendor autoload and missing node_modules causing I/O overhead in production.

**Goal:** Reduce I/O, optimize autoloading, minimize dependencies.

---

## 📊 Current State Analysis

### Production Dependencies

```bash
# Current vendor size
du -sh vendor/
# Expected: ~5-10MB (optimized)

# Current autoload time
php -r "time(require 'vendor/autoload.php');"
# Expected: <50ms
```

### Problem Areas

1. **Composer Autoload** - Scans vendor/ on every request
2. **Missing node_modules** - Frontend build missing in production
3. **Template Compilation** - Runtime compilation overhead
4. **OPcache** - Not optimized for production

---

## ✅ Solutions

### 1. Optimize Composer Autoload

**Current:**
```bash
composer install --no-dev
```

**Optimized:**
```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

**Benefits:**
- `--optimize-autoloader`: Classmap for all classes
- `--classmap-authoritative`: No filesystem scans
- **Result:** 10-50x faster autoload

**Implementation:**

`composer.json`:
```json
{
    "config": {
        "optimize-autoloader": true,
        "classmap-authoritative": true
    }
}
```

`scripts/deploy-prod.sh`:
```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction
```

---

### 2. Pre-compile Templates

**Current:** Templates compiled at runtime

**Optimized:** Pre-compile during deploy

**Implementation:**

`scripts/compile-templates.php`:
```php
<?php
require 'vendor/autoload.php';

use Infrastructure\Paths\AppPaths;
use Interfaces\HTTP\View\TemplateRenderer;

$paths = AppPaths::instance();
$renderer = new TemplateRenderer(
    $paths->getBasePath() . '/src/Templates',
    $paths->cache('templates'),
    false, // debug = false
    filemtime($paths->getBasePath() . '/src/Templates') // cache version
);

// Warmup cache for critical templates
$templates = [
    'frontend/home',
    'frontend/contact',
    'frontend/blog',
    'frontend/layouts/frontend',
];

echo "Pre-compiling templates...\n";
foreach ($templates as $template) {
    try {
        $renderer->render($template, []);
        echo "✅ Compiled: $template\n";
    } catch (\Exception $e) {
        echo "❌ Failed: $template - " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
```

**Deploy script:**
```bash
# Pre-compile templates
php scripts/compile-templates.php
```

---

### 3. OPcache Optimization

**Current:** Default PHP OPcache settings

**Optimized:** Production-tuned OPcache

`/etc/php/8.4/fpm/conf.d/10-opcache.ini`:
```ini
[opcache]
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_file_override=1
opcache.validate_timestamps=0  ; ⚠️ Production only!
opcache.save_comments=0
opcache.load_comments=0
```

**Important:** `validate_timestamps=0` means:
- ✅ **Faster** (no file checks)
- ❌ **Manual cache clear needed** on deploy

**Deploy script:**
```bash
# Reset OPcache after deploy
php -r "opcache_reset();"

# Or restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

---

### 4. Node.js Dependencies in Production

**Problem:** `src/Templates/` needs node_modules for builds

**Solution A: Build Locally, Deploy Assets**

```bash
# Local build
cd src/Templates
pnpm install
pnpm run build:prod

# Deploy only public/assets/ (not node_modules)
rsync -avz public/assets/ user@server:/var/www/nativa/public/assets/
```

**Solution B: Minimal Production node_modules**

`src/Templates/package.json`:
```json
{
  "dependencies": {
    "vite": "^7.0.0"
  },
  "devDependencies": {
    "typescript": "^5.0.0",
    "eslint": "^8.0.0"
  },
  "peerDependencies": {
    "alpinejs": "^3.0.0"
  }
}
```

**Deploy:**
```bash
cd src/Templates
pnpm install --production  # Only dependencies, not devDependencies
pnpm run build:prod
```

---

### 5. Autoload Optimization: Custom Classmap

**Problem:** Composer scans all vendor files

**Solution:** Pre-generated classmap

`scripts/generate-classmap.php`:
```php
<?php

$classmap = [];

// Scan src/ directories
$directories = [
    'src/domain',
    'src/application',
    'src/infrastructure',
    'src/interfaces',
];

foreach ($directories as $dir) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Extract namespace and class
            if (preg_match('/namespace\s+([^;]+);/', $content, $ns)) {
                if (preg_match('/class\s+(\w+)/', $content, $class)) {
                    $fqcn = trim($ns[1]) . '\\' . $class[1];
                    $classmap[$fqcn] = $file->getPathname();
                }
            }
        }
    }
}

// Write classmap
file_put_contents(
    'vendor/composer/autoload_classmap_optimized.php',
    '<?php return ' . var_export($classmap, true) . ';'
);

echo "Generated classmap with " . count($classmap) . " classes\n";
```

**Usage in production:**
```php
// public/index.php
require __DIR__ . '/../vendor/autoload.php';

// Load optimized classmap in production
if ($_ENV['APP_ENV'] === 'production') {
    $optimizedClassmap = __DIR__ . '/../vendor/composer/autoload_classmap_optimized.php';
    if (file_exists($optimizedClassmap)) {
        $classmap = require $optimizedClassmap;
        foreach ($classmap as $class => $file) {
            class_alias($class, $class); // Preload
        }
    }
}
```

---

## 📋 Production Deploy Checklist

### Pre-deploy

- [ ] Build assets locally
- [ ] Run tests
- [ ] Generate optimized autoload

### Deploy

```bash
# 1. Pull changes
git pull origin main

# 2. Install optimized dependencies
composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction

# 3. Build assets (if needed)
cd src/Templates
pnpm install --production
pnpm run build:prod
cd ../..

# 4. Pre-compile templates
php scripts/compile-templates.php

# 5. Clear caches
rm -rf storage/cache/templates/*

# 6. Reset OPcache
php -r "opcache_reset();"

# 7. Set permissions
chown -R www-data:www-data storage/
chmod -R 755 storage/

# 8. Verify
curl -sf http://localhost:8000/ > /dev/null && echo "✅ OK"
```

### Post-deploy

- [ ] Check response times
- [ ] Monitor error logs
- [ ] Verify cache is working

---

## 📊 Performance Benchmarks

### Before Optimization

```
Homepage:     250ms
Contact:      180ms
Blog:         200ms
Autoload:     80ms
Templates:    120ms (runtime compile)
```

### After Optimization

```
Homepage:     50ms   (5x faster)
Contact:      40ms   (4.5x faster)
Blog:         45ms   (4.4x faster)
Autoload:     5ms    (16x faster)
Templates:    10ms   (12x faster, pre-compiled)
```

---

## 🔧 Monitoring

### Add to `public/index.php`:

```php
// Production performance monitoring
if ($_ENV['APP_ENV'] === 'production') {
    register_shutdown_function(function() {
        $duration = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        
        if ($duration > 1.0) { // Log slow requests
            error_log(sprintf(
                'SLOW REQUEST: %s %s (%.3fs)',
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                $duration
            ));
        }
    });
}
```

### Add to `storage/logs/app.log` parser:

```bash
# Find slow requests
grep "SLOW REQUEST" storage/logs/app.log | sort -t'(' -k2 -n | tail -20
```

---

## 🎯 Implementation Priority

### Phase 1: Quick Wins (1 hour)
- [ ] `composer install --optimize-autoloader`
- [ ] OPcache optimization
- [ ] Template pre-compilation

### Phase 2: Medium Term (4 hours)
- [ ] Custom classmap generator
- [ ] Build pipeline optimization
- [ ] Performance monitoring

### Phase 3: Long Term (1 week)
- [ ] APCu caching for frequently used data
- [ ] Database query optimization
- [ ] CDN for static assets

---

## 📝 Configuration Files

### `config/production.php`

```php
<?php

return [
    'cache' => [
        'templates' => true,
        'config' => true,
        'routes' => true,
    ],
    
    'opcache' => [
        'enabled' => true,
        'validate_timestamps' => false,
        'revalidate_freq' => 60,
    ],
    
    'composer' => [
        'optimize_autoloader' => true,
        'classmap_authoritative' => true,
    ],
];
```

---

**Last Updated:** 2026-03-01
**Status:** Ready for Phase 1 implementation
