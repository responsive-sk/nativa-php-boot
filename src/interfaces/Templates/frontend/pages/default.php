<?php
/**
 * Frontend Default Page Template
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var \Domain\Model\Page $page
 * @var array $blocks
 * @var array $media
 * @var array $forms
 */
?>
<!-- Hero Section (if block exists) -->
<?php foreach ($blocks as $block): ?>
    <?php if ($block->type() === 'hero'): ?>
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <?php if ($block->title()): ?>
            <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= $this->e($block->title()) ?></h1>
            <?php endif; ?>
            <?php if ($block->content()): ?>
            <p class="text-xl md:text-2xl opacity-90"><?= $this->e($block->content()) ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Page Content -->
        <div class="<?= !empty($forms) ? 'lg:w-2/3' : 'lg:w-3/4' ?> mx-auto">
            <article class="prose max-w-none">
                <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= $this->e($page->title()) ?></h1>
                
                <div class="text-gray-700 leading-relaxed">
                    <?= nl2br($this->e($page->content())) ?>
                </div>
            </article>

            <!-- Content Blocks -->
            <?php foreach ($blocks as $block): ?>
                <?php if (!$block->isActive()) continue; ?>
                
                <?php if ($block->type() === 'features'): ?>
                <section class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $this->e($block->title() ?? 'Features') ?></h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?= $block->content() ?>
                    </div>
                </section>
                
                <?php elseif ($block->type() === 'cta'): ?>
                <section class="mt-12 bg-blue-50 rounded-lg p-8 text-center">
                    <?php if ($block->title()): ?>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4"><?= $this->e($block->title()) ?></h2>
                    <?php endif; ?>
                    <?php if ($block->content()): ?>
                    <p class="text-gray-600 mb-6"><?= $this->e($block->content()) ?></p>
                    <?php endif; ?>
                </section>
                
                <?php elseif ($block->type() === 'text_image'): ?>
                <section class="mt-12 grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <?= nl2br($this->e($block->content() ?? '')) ?>
                    </div>
                    <?php 
                    $imageData = $block->data()['image'] ?? null;
                    if ($imageData): 
                    ?>
                    <img src="<?= $this->e($imageData) ?>" alt="<?= $this->e($block->title() ?? '') ?>" class="rounded-lg shadow-md" />
                    <?php endif; ?>
                </section>
                
                <?php elseif ($block->type() === 'testimonials'): ?>
                <section class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $this->e($block->title() ?? 'Testimonials') ?></h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <?= $block->content() ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Page Media Gallery -->
            <?php if (!empty($media)): ?>
            <section class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Gallery</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($media as $item): ?>
                    <div class="group relative overflow-hidden rounded-lg">
                        <img src="<?= $this->e($item->url()) ?>" alt="<?= $this->e($item->caption() ?? '') ?>" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300" />
                        <?php if ($item->caption()): ?>
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <p class="text-white text-sm"><?= $this->e($item->caption()) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar with Forms -->
        <?php if (!empty($forms)): ?>
        <aside class="lg:w-1/3">
            <div class="sticky top-4 space-y-6">
                <?php foreach ($forms as $form): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <?php if ($form->title()): ?>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4"><?= $this->e($form->title()) ?></h3>
                    <?php endif; ?>
                    
                    <!-- Form placeholder - would need form renderer -->
                    <div class="text-sm text-gray-500">
                        Form: <?= $this->e($form->formName()) ?>
                        <br/>
                        <a href="/form/<?= $this->e($form->formSlug()) ?>" class="text-blue-600 hover:underline">
                            Open Form →
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </aside>
        <?php endif; ?>
    </div>
</main>

<!-- Bottom CTA Block -->
<?php foreach ($blocks as $block): ?>
    <?php if ($block->type() === 'cta' && $block->data()['position'] === 'bottom'): ?>
    <section class="bg-gray-800 text-white py-12 mt-12">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-2xl font-bold mb-4"><?= $this->e($block->title()) ?></h2>
            <p class="text-lg opacity-90"><?= $this->e($block->content()) ?></p>
        </div>
    </section>
    <?php endif; ?>
<?php endforeach; ?>
