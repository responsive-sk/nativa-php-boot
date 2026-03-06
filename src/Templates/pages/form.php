<?php declare(strict_types = 1);

/**
 * Dynamic Form Template.
 *
 * @var object $form Form entity with schema
 * @var string $title Form title
 */
$form ??= null;
$title ??= 'Form';

?>

<?= $this->yieldContent() ?>
