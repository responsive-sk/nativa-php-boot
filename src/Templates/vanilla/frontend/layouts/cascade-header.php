<?php
declare(strict_types = 1);

/**
 * Cascade Layout - Using Cascade Framework
 *
 * @var string      $content
 * @var string      $pageTitle
 * @var string|null $metaDescription
 */

$pageTitle ??= 'Nativa CMS';
$metaDescription ??= 'Modern PHP CMS and Blog Platform';

use Infrastructure\View\AssetHelper;

// Load core CSS which includes Cascade Framework
$coreCss = AssetHelper::css('core-css');
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($metaDescription) ?>">

  <title><?= e($pageTitle) ?></title>
  
  <!-- Cascade Framework CSS (included in core-css) -->
  <link rel="stylesheet" href="<?= $coreCss ?>">
  
  <!-- Cascade Site CSS (demo styles) -->
  <link rel="stylesheet" href="/assets/frontend/site.css">
</head>
<body>
  <div class="site-center">
    
    <!-- Header -->
    <div class="site-header">
      <div class="col width-fill">
        <div class="col width-fit">
          <div class="cell">
            <a href="/" class="logo">
              <span>Nativa</span>
              <span class="logo-dot">•</span>
              <span>CMS</span>
            </a>
          </div>
        </div>
        <div class="col width-fill">
          <div class="cell">
            <ul class="nav">
              <li><a href="/">Home</a></li>
              <li><a href="/blog">Blog</a></li>
              <li><a href="/portfolio">Portfolio</a></li>
              <li><a href="/contact">Contact</a></li>
              <li><a href="/docs">Docs</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
