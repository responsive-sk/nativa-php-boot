<?php
declare(strict_types = 1);

/**
 * Footer Partial
 *
 * @var int $year Current year
 */
$year ??= date('Y');
?>

<!-- Footer -->
<footer class="footer">
  <div class="footer__inner container">
    <div class="footer__content">
      <div class="footer__section">
        <h3 class="footer__title">Nativa CMS</h3>
        <ul class="footer__links">
          <li><a href="/">Home</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer__section">
        <h3 class="footer__title">Features</h3>
        <ul class="footer__links">
          <li><a href="/blog">Articles</a></li>
          <li><a href="/blog">Pages</a></li>
          <li><a href="/contact">Forms</a></li>
        </ul>
      </div>
      <div class="footer__section">
        <h3 class="footer__title">Support</h3>
        <ul class="footer__links">
          <li><a href="/contact">Contact</a></li>
          <li><a href="/docs">Documentation</a></li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <p>&copy; <?= $year ?> Nativa CMS. All rights reserved.</p>
    </div>
  </div>
</footer>
