<?php declare(strict_types = 1);

/**
 * Articles Listing Template.
 *
 * @var array  $articles Array of Article entities
 * @var string $title Page title
 * @var string $searchQuery Search query (optional)
 */
$articleList = $articles ?? [];
$title ??= 'Articles';
$searchQuery ??= '';

?>

<?= $this->yieldContent() ?>
