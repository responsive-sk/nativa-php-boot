<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Frontend;

use Symfony\Component\HttpFoundation\Response;

/**
 * Static Page Controller
 */
class PageController
{
    public function show(string $slug): Response
    {
        // TODO: Load page from database
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$slug}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">{$slug}</h1>
            <div class="prose max-w-none">
                <p>Page content coming soon...</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return new Response($html);
    }
}
