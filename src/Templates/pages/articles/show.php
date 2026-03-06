<?php declare(strict_types = 1);

/**
 * Article Detail Template.
 *
 * @var object $article Article entity
 * @var string $title Page title
 */
$article ??= null;

?>

<?= $this->yieldContent() ?>
