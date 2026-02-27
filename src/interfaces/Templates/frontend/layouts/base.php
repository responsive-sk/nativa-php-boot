<?php
/**
 * Base Layout
 * 
 * @var TemplateRenderer $this
 * @var string $title
 * @var string $content
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'PHP CMS') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="/" class="text-2xl font-bold text-gray-800">PHP CMS</a>
                <div class="flex gap-6">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="/articles" class="text-gray-600 hover:text-gray-900">Articles</a>
                    <a href="/contact" class="text-gray-600 hover:text-gray-900">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> PHP CMS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
