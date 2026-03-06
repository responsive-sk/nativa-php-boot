<?php
declare(strict_types = 1);

/**
 * CMS Layout - For PHP CMS pages with dynamic content.
 *
 * @var string      $content
 * @var string      $page Page identifier (home, blog, contact, etc.)
 * @var string      $pageTitle Page title (dynamic from CMS)
 * @var string|null $metaDescription Optional meta description
 * @var bool        $isGuest User authentication state
 * @var string      $csrfToken CSRF token for forms
 */

use Infrastructure\View\AssetHelper;

$page ??= 'home';
$pageTitle ??= 'Nativa CMS';
$isGuest ??= true;
$csrfToken ??= '';
$metaDescription ??= 'Modern PHP CMS and Blog Platform';

// Use AssetHelper for production builds with hashed filenames
$themeInitJs = AssetHelper::js('init.js');
$cssBundle = AssetHelper::css('css.css');
$appJs = AssetHelper::js('app.js');

// Page-specific CSS - dynamically loaded from manifest.json
$pageSpecificCssUrl = AssetHelper::pageCss($page);

?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
  <meta name="description" content="<?php echo $this->e($metaDescription); ?>">
  <meta name="referrer" content="strict-origin-when-cross-origin">

  <!-- Dynamic page title from CMS -->
  <title><?php echo $this->e($pageTitle); ?></title>

  <!-- Prevent theme flash - load theme script before CSS -->
  <script src="<?php echo $themeInitJs; ?>" defer crossorigin="anonymous"></script>

  <!-- CRITICAL CSS (inlined for faster FCP) -->
  <?php
  $criticalCssFile = __DIR__ . '/storage/critical-css/critical.css';
if (file_exists($criticalCssFile)) {
    echo '<style id="critical-css">' . file_get_contents($criticalCssFile) . '</style>';
}
?>

  <!-- Shared base CSS (async loaded) -->
  <link rel="preload" href="<?php echo $cssBundle; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="<?php echo $cssBundle; ?>"></noscript>

  <!-- Page-specific CSS (async loaded, only if exists) -->
  <?php if ($pageSpecificCssUrl) { ?>
  <link rel="preload" href="<?php echo $pageSpecificCssUrl; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="<?php echo $pageSpecificCssUrl; ?>"></noscript>
  <?php } ?>

  <!-- Preload critical fonts for hero (ONLY essential fonts) -->
  <link rel="preload" href="/assets/fonts/sans-serif/font-sans-web.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/assets/fonts/serif/font-serif-web.woff2" as="font" type="font/woff2" crossorigin>

</head>
<body>
  <!-- Header Partial -->
  <?php include $this->getTemplatesPath() . '/partials/header.php'; ?>

  <!-- Hero Section (only for homepage) -->
  <?php if (($page ?? '') === 'home') { ?>
    <?php include $this->getTemplatesPath() . '/partials/hero-home.php'; ?>
  <?php } ?>

  <!-- Main Content -->
  <main class="main">
    <?php echo $content; ?>
  </main>

  <!-- Footer Partial -->
  <?php include $this->getTemplatesPath() . '/partials/footer.php'; ?>

  <!-- Shared JavaScript -->
  <script type="module" src="<?php echo $appJs; ?>"></script>

  <!-- Page-specific JavaScript (if exists) -->
  <?php
$pageSpecificJs = [
    'home'      => AssetHelper::js('home'),
    'blog'      => AssetHelper::js('blog'),
    'portfolio' => AssetHelper::js('portfolio'),
    'contact'   => AssetHelper::js('contact'),
    'docs'      => AssetHelper::js('docs'),
    'about'     => AssetHelper::js('about'),
    'services'  => AssetHelper::js('services'),
    'pricing'   => AssetHelper::js('pricing'),
];
?>
  <?php foreach ($pageSpecificJs as $pageName => $jsFile) { ?>
  <?php if (($page ?? '') === $pageName && $jsFile) { ?>
  <script type="module" src="<?php echo $jsFile; ?>" defer crossorigin="anonymous"></script>
  <?php } ?>
  <?php } ?>

</body>
</html>
