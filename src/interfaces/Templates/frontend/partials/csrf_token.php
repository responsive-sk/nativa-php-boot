<?php
/**
 * CSRF Token Input Partial
 *
 * Usage: <?= $this->partial('csrf_token.php') ?>
 */
?>
<input type="hidden" name="_token" value="<?= htmlspecialchars(\Application\Middleware\CsrfMiddleware::token(), ENT_QUOTES, 'UTF-8') ?>">
