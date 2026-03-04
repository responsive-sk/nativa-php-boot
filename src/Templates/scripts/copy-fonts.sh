#!/bin/bash
# Post-build script - Copy optimized fonts to public/assets
# Run after `pnpm run build`

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "📦 Copying optimized fonts to public/assets..."

# Create directories
mkdir -p "$PROJECT_ROOT/public/assets/fonts/sans-serif"
mkdir -p "$PROJECT_ROOT/public/assets/fonts/serif"

# Copy sans-serif fonts (including legacy)
cp "$SCRIPT_DIR/src/assets/fonts/sans-serif"/*.woff2 "$PROJECT_ROOT/public/assets/fonts/sans-serif/"

# Copy serif fonts
cp "$SCRIPT_DIR/src/assets/fonts/serif"/*.woff2 "$PROJECT_ROOT/public/assets/fonts/serif/"

echo "✅ Fonts copied successfully!"
echo ""
echo "📁 Sans-serif fonts:"
ls -lh "$PROJECT_ROOT/public/assets/fonts/sans-serif/"
echo ""
echo "📁 Serif fonts:"
ls -lh "$PROJECT_ROOT/public/assets/fonts/serif/"
