<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Contact Form Controller
 */
class ContactController
{
    public function show(): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Contact Us</h1>
            
            <form id="contactForm" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" id="name" name="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" id="subject" name="subject"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Message *</label>
                    <textarea id="message" name="message" rows="5" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border"></textarea>
                </div>
                
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Send Message
                </button>
            </form>
            
            <div id="success" class="hidden mt-4 p-4 bg-green-100 text-green-700 rounded-md">
                Thank you for your message! We'll get back to you soon.
            </div>
            
            <div id="error" class="hidden mt-4 p-4 bg-red-100 text-red-700 rounded-md"></div>
        </div>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                });
                
                if (response.ok) {
                    document.getElementById('success').classList.remove('hidden');
                    form.reset();
                } else {
                    const data = await response.json();
                    document.getElementById('error').textContent = data.error || 'Something went wrong';
                    document.getElementById('error').classList.remove('hidden');
                }
            } catch (err) {
                document.getElementById('error').textContent = 'Network error. Please try again.';
                document.getElementById('error').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
HTML;

        return new Response($html);
    }

    public function submit(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }
            if (empty($data['message'])) {
                $errors[] = 'Message is required';
            }

            if (!empty($errors)) {
                return new JsonResponse(['error' => implode(', ', $errors)], 400);
            }

            // TODO: Save to database and send email

            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Something went wrong'], 500);
        }
    }
}
