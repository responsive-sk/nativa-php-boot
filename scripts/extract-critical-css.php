<?php
/**
 * Critical CSS Extractor
 * 
 * Extracts above-the-fold CSS for inline embedding
 * Usage: php scripts/extract-critical-css.php [page]
 */

declare(strict_types=1);

$criticalRules = [
    // Reset & base
    ':root',
    '*,*::before,*::after',
    'html',
    'body',
    
    // Navigation (always visible)
    '.nav-primary',
    '.nav-primary__inner',
    '.nav-primary__logo',
    '.nav-primary__list',
    '.nav-primary__link',
    '.nav-primary__mobile-toggle',
    '.mobile-toggle__icon',
    
    // Hero sections
    '.hero-manifesto',
    '.hero-manifesto__bg',
    '.hero-manifesto__content',
    '.hero-manifesto__text',
    '.blog-hero',
    '.blog-hero__bg',
    '.blog-hero__content',
    '.blog-hero__title',
    
    // Typography
    '.anim-block',
    '.anim-block__line',
    '.anim-block__inner',
    '.anim-block__text',
    '.anim-block--hero',
    '.anim-block--heading',
    
    // Buttons (CTA in hero)
    '.btn',
    '.btn--primary',
    '.btn--outline',
    
    // Layout
    '.container',
    '.site-body',
    
    // Theme toggle
    '.theme-toggle',
    
    // Mobile menu (critical for mobile)
    '.mobile-menu',
    '.mobile-menu__inner',
];

$basePath = __DIR__ . '/../src/Templates';
$cssFiles = [
    $basePath . '/src/styles/shared/design-tokens.css',
    $basePath . '/src/styles/components/header.css',
    $basePath . '/src/styles/components/button.css',
    $basePath . '/src/styles/components/hero.css',
    $basePath . '/src/styles/components/anim-block.css',
    $basePath . '/src/styles/shared/fonts.css',
];

$criticalCSS = '';

foreach ($cssFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $css = file_get_contents($file);
    $lines = explode("\n", $css);
    
    foreach ($lines as $line) {
        foreach ($criticalRules as $rule) {
            if (strpos($line, $rule) !== false) {
                $criticalCSS .= $line . "\n";
                break;
            }
        }
    }
}

// Minify basic whitespace
$criticalCSS = preg_replace('/\s+/', ' ', $criticalCSS);
$criticalCSS = preg_replace('/\s*([{};:])\s*/', '$1', $criticalCSS);

$outputDir = $basePath . '/storage/critical-css';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Save critical CSS
file_put_contents($outputDir . '/critical.css', $criticalCSS);

echo "✅ Critical CSS extracted: " . strlen($criticalCSS) . " bytes\n";
echo "📁 Saved to: $outputDir/critical.css\n";

// Show savings
$fullCSS = '';
foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        $fullCSS .= file_get_contents($file);
    }
}
$fullSize = strlen($fullCSS);
$savings = 100 - (strlen($criticalCSS) / $fullSize * 100);

echo "📊 Full CSS: " . number_format($fullSize) . " bytes\n";
echo "📊 Critical CSS: " . number_format(strlen($criticalCSS)) . " bytes\n";
echo "💾 Savings: " . number_format($savings, 1) . "% not inlined\n";
