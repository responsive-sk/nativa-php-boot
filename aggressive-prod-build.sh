#!/bin/bash
# AGGRESSIVE vendor optimization for production
# WARNING: This removes more files. Test thoroughly before deploying!

set -e

echo "AGGRESSIVE Production optimization starting..."
echo "This will remove extra files for maximum space savings"
echo ""

if [ ! -d "vendor/" ]; then
    echo "Error: vendor/ directory not found"
    exit 1
fi

# Get project root size before
echo "Scanning project for node_modules directories..."
PROJECT_BEFORE=$(du -sh . 2>/dev/null | awk '{print $1}')
VENDOR_BEFORE=$(du -sh vendor/ | awk '{print $1}')

echo "Project size before: $PROJECT_BEFORE"
echo "Vendor size before:  $VENDOR_BEFORE"
echo ""

# Run standard optimization first
echo "Applying AGGRESSIVE optimizations..."

# Remove ALL node_modules directories GLOBALLY (root, themes, everywhere)
echo "  → Removing ALL node_modules directories globally..."
# Find and count all node_modules
NODE_MODULES_FOUND=$(find . -type d -name "node_modules" -not -path "*/vendor/*" 2>/dev/null)
NODE_COUNT=$(echo "$NODE_MODULES_FOUND" | grep -c "node_modules" || true)

if [ "$NODE_COUNT" -gt 0 ]; then
    echo "     Found $NODE_COUNT node_modules directories:"
    echo "$NODE_MODULES_FOUND" | while read -r dir; do
        if [ -n "$dir" ]; then
            SIZE=$(du -sh "$dir" 2>/dev/null | awk '{print $1}')
            echo "       - $dir ($SIZE)"
        fi
    done
    echo ""
    echo "     Removing all node_modules..."
    find . -type d -name "node_modules" -exec rm -rf {} + 2>/dev/null || true
    echo "     ✓ All node_modules removed"
else
    echo "     No node_modules found outside vendor/"
fi
echo ""

# Now process vendor directory
echo "Processing vendor/ directory..."
BEFORE=$(du -sh vendor/ | awk '{print $1}')
echo "Vendor before optimization: $BEFORE"
echo ""

# Remove example/demo files
echo "  → Removing examples and demos..."
find vendor/ -type d -name "example" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "examples" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "demo" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "demos" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "sample" -exec rm -rf {} + 2>/dev/null || true

# Remove benchmark files
echo "  → Removing benchmarks..."
find vendor/ -type d -name "benchmark" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "benchmarks" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -name "*Bench.php" -delete 2>/dev/null || true

# Remove stubs and fixtures
echo "  → Removing stubs and fixtures..."
find vendor/ -type d -name "stubs" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "fixtures" -exec rm -rf {} + 2>/dev/null || true
find vendor/ -type d -name "Fixtures" -exec rm -rf {} + 2>/dev/null || true

# Remove dev tools
echo "  → Removing dev tools..."
find vendor/bin -type f ! -name "doctrine" -delete 2>/dev/null || true

# Remove .dist files
echo "  → Removing .dist files..."
find vendor/ -name "*.dist" -delete 2>/dev/null || true

# Remove editor configs
echo "  → Removing editor configs..."
find vendor/ -name ".editorconfig" -delete 2>/dev/null || true
find vendor/ -name ".eslintrc*" -delete 2>/dev/null || true
find vendor/ -name ".prettierrc*" -delete 2>/dev/null || true

# Remove package manager files
echo "  → Removing package manager files..."
find vendor/ -name "package.json" -delete 2>/dev/null || true
find vendor/ -name "package-lock.json" -delete 2>/dev/null || true
find vendor/ -name "yarn.lock" -delete 2>/dev/null || true
find vendor/ -name "composer.lock" -delete 2>/dev/null || true

# Remove node_modules in vendor (if any remain)
echo "  → Removing node_modules in vendor..."
NODE_VENDOR_COUNT=$(find vendor/ -type d -name "node_modules" 2>/dev/null | wc -l)
if [ "$NODE_VENDOR_COUNT" -gt 0 ]; then
    echo "     Found $NODE_VENDOR_COUNT node_modules in vendor/"
    find vendor/ -type d -name "node_modules" -exec rm -rf {} + 2>/dev/null || true
    echo "     ✓ Removed"
else
    echo "     No node_modules in vendor/"
fi

# Remove Windows-specific files
echo "  → Removing Windows files..."
find vendor/ -name "*.bat" -delete 2>/dev/null || true
find vendor/ -name "*.cmd" -delete 2>/dev/null || true

# Remove Makefile (if not needed)
echo "  → Removing build files..."
find vendor/ -name "Makefile" -delete 2>/dev/null || true
find vendor/ -name "makefile" -delete 2>/dev/null || true
find vendor/ -type d -name "build" -exec rm -rf {} + 2>/dev/null || true

# Remove backup files
echo "  → Removing backup files..."
find vendor/ -name "*~" -delete 2>/dev/null || true
find vendor/ -name "*.bak" -delete 2>/dev/null || true
find vendor/ -name "*.swp" -delete 2>/dev/null || true

# Remove large unnecessary image files (keep small icons)
echo "  → Removing large images..."
find vendor/ -name "*.png" -size +100k -delete 2>/dev/null || true
find vendor/ -name "*.jpg" -size +100k -delete 2>/dev/null || true
find vendor/ -name "*.gif" -size +100k -delete 2>/dev/null || true

# Remove unnecessary language files from Carbon (keep only en, en_US, cs, sk)
echo "  → Removing unnecessary Carbon language files..."
CARBON_LANG_DIR="vendor/nesbot/carbon/src/Carbon/Lang"
if [ -d "$CARBON_LANG_DIR" ]; then
    # Count before
    LANG_BEFORE=$(find "$CARBON_LANG_DIR" -type f -name "*.php" | wc -l)
    
    # Remove all language files except en.php, en_US.php, cs.php, and sk.php
    find "$CARBON_LANG_DIR" -type f -name "*.php" \
        ! -name "en.php" \
        ! -name "en_US.php" \
        ! -name "cs.php" \
        ! -name "sk.php" \
        -delete 2>/dev/null || true
    
    # Count after
    LANG_AFTER=$(find "$CARBON_LANG_DIR" -type f -name "*.php" | wc -l)
    LANG_REMOVED=$((LANG_BEFORE - LANG_AFTER))
    
    echo "     Removed $LANG_REMOVED language files (kept 4: en, en_US, cs, sk)"
else
    echo "     Carbon language directory not found, skipping..."
fi

# Clean up empty directories again
echo "  → Final cleanup of empty directories..."
find vendor/ -type d -empty -delete 2>/dev/null || true

echo ""
echo "═══════════════════════════════════════════════════════"
echo "AGGRESSIVE optimization complete!"
echo "═══════════════════════════════════════════════════════"
echo ""
AFTER=$(du -sh vendor/ | awk '{print $1}')
PROJECT_AFTER=$(du -sh . 2>/dev/null | awk '{print $1}')

echo "VENDOR DIRECTORY:"
echo "  Before: $BEFORE"
echo "  After:  $AFTER"
echo ""
echo "TOTAL PROJECT:"
echo "  Before: $PROJECT_BEFORE"
echo "  After:  $PROJECT_AFTER"
echo ""
echo "Done! Test your application thoroughly."