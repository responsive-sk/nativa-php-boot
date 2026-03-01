# Frontend Build Guide

Vite-based build system for Yii Boot frontend assets.

## Quick Start

### Development

```bash
cd views/templates

# Install dependencies
npm install

# Start dev server with HMR
npm run dev
```

### Production Build

```bash
cd views/templates

# Install dependencies
npm install

# Build for production (minified, no sourcemaps, hashed filenames)
npm run build:prod

# Or build for development (unminified, with sourcemaps)
npm run build:dev
```

## Build Scripts

| Command              | Description                                |
| -------------------- | ------------------------------------------ |
| `npm run dev`        | Start development server with hot reload   |
| `npm run build`      | Default build (development mode)           |
| `npm run build:dev`  | Development build with sourcemaps          |
| `npm run build:prod` | **Production build** - minified, optimized |
| `npm run build:mark` | Markdown preview build                     |
| `npm run preview`    | Preview production build locally           |
| `npm run analyze`    | Analyze bundle size                        |
| `npm run type-check` | TypeScript type checking                   |
| `npm run lint`       | ESLint code analysis                       |
| `npm run format:fix` | Format code with Prettier                  |

## Production Build Features

When running `npm run build:prod`:

✅ **Minification** - Terser JS minifier
✅ **No Sourcemaps** - Smaller file sizes
✅ **Hashed Filenames** - Cache busting (`app.a1b2c3d4.js`)
✅ **CSS Code Splitting** - Page-specific CSS
✅ **Gzip Compression** - `.gz` files
✅ **Brotli Compression** - `.br` files (better compression)
✅ **Manifest File** - Asset versioning for PHP

## Output Structure

```
public/assets/
├── manifest.json          # Asset version mapping
├── init.js          # Theme initialization
├── main.[hash].js         # Main entry point
├── main.[hash].css        # Main styles
├── app.[hash].js          # Shared utilities
├── home.[hash].js         # Homepage-specific
├── use-cases/
│   ├── services.[hash].js
│   ├── pricing.[hash].js
│   └── ...
├── fonts/                 # Font files
├── images/                # Images and icons
└── *.gz, *.br            # Compressed versions
```

## Environment Variables

Create `.env` file in `views/templates/`:

```bash
# Base URL for assets (change for CDN)
VITE_ASSET_BASE=/assets/

# Feature flags
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_ERROR_LOGGING=false

# API endpoints
VITE_API_URL=https://api.example.com
```

### Environment Files

- `.env` - Local development (git tracked)
- `.env.example` - Template for new developers
- `.env.production` - Production settings (git tracked)
- `.env.local` - Local overrides (git ignored)

## Deployment

### 1. Build Assets

```bash
cd views/templates
npm install --production
npm run build:prod
```

### 2. Deploy Files

Copy these to production server:

```
public/assets/           # All built assets
views/templates/.env.production
```

### 3. Set Environment

On production server:

```bash
cd views/templates
cp .env.production .env
```

### 4. Verify Build

Check `public/assets/manifest.json`:

```json
{
    "main.js": {
        "file": "main.a1b2c3d4.js",
        "src": "main.js",
        "isEntry": true
    }
}
```

## Cache Busting

Production builds use content hashes:

- Filenames change when content changes
- Browser caches forever (`Cache-Control: public, max-age=31536000`)
- Manifest file tracks current versions
- PHP reads manifest for correct filenames

## Compression

Two compression algorithms are used:

### Gzip (`.gz`)

- Universal support
- Fast compression/decompression
- ~60-70% size reduction

### Brotli (`.br`)

- Better compression (~15-20% smaller than gzip)
- Slower compression, fast decompression
- Modern browsers support

Configure web server to serve compressed files:

**Nginx:**

```nginx
location /assets/ {
    gzip_static on;
    brotli_static on;
}
```

**Apache:**

```apache
<IfModule mod_headers.c>
    Header set Content-Encoding gzip
    <FilesMatch "\.gz$">
        ForceType application/javascript
    </FilesMatch>
</IfModule>
```

## Performance Tips

### 1. Analyze Bundle

```bash
npm run analyze
```

Opens interactive bundle size visualization.

### 2. Check File Sizes

```bash
# After build
ls -lh public/assets/*.js
```

### 3. Enable HTTP/2

HTTP/2 multiplexing improves asset loading.

### 4. Use CDN

Set `VITE_ASSET_BASE` to CDN URL:

```bash
VITE_ASSET_BASE=https://cdn.example.com/assets/
```

## Troubleshooting

### Issue: Assets not loading after deploy

**Solution**: Check `manifest.json` is updated and paths are correct

### Issue: Old CSS/JS still showing

**Solution**: Clear browser cache or check hash in manifest

### Issue: Build fails with TypeScript errors

**Solution**: Run `npm run type-check` to see errors

### Issue: Large bundle size

**Solution**: Run `npm run analyze` to find bottlenecks

## Development vs Production

| Feature      | Development | Production      |
| ------------ | ----------- | --------------- |
| Minification | ❌ No       | ✅ Yes (Terser) |
| Sourcemaps   | ✅ Yes      | ❌ No           |
| File Hashes  | ❌ No       | ✅ Yes          |
| Build Time   | ~5s         | ~15s            |
| Bundle Size  | ~500KB      | ~150KB          |
| HMR          | ✅ Yes      | ❌ No           |

## Integration with Yii

PHP reads `manifest.json` to get correct asset URLs:

```php
$manifest = json_decode(file_get_contents('@public/assets/manifest.json'), true);
$mainJs = $manifest['main.js']['file']; // "main.a1b2c3d4.js"
```

See `views/templates/layouts/main.php` for implementation.

---

**Last Updated**: 2026-02-18
**Vite Version**: 7.x
**Node Version**: 18+
