<?php
/**
 * Admin Base Layout
 * 
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var string $content
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Admin') ?> - PHP CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Admin Sidebar -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-xl font-bold">PHP CMS Admin</h1>
            </div>
            <nav class="mt-4 flex flex-col h-full justify-between">
                <div>
                    <a href="/admin" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
                    <a href="/admin/articles" class="block px-4 py-2 hover:bg-gray-700">Articles</a>
                    <a href="/admin/forms" class="block px-4 py-2 hover:bg-gray-700">Forms</a>
                    <a href="/admin/pages" class="block px-4 py-2 hover:bg-gray-700">Pages</a>
                    <a href="/admin/media" class="block px-4 py-2 hover:bg-gray-700">Media</a>
                    <a href="/admin/settings" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
                </div>
                
                <!-- Logout Button -->
                <div class="mt-auto border-t border-gray-700">
                    <a href="/logout" class="block px-4 py-2 text-red-400 hover:bg-gray-700 hover:text-red-300">
                        ← Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <?= $content ?>
        </main>
    </div>
</body>
</html>
