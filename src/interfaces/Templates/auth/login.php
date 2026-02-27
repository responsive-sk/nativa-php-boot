<?php
/**
 * Login Template
 *
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $title
 * @var string|null $error
 * @var array $old
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title) ?> - PHP CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">PHP CMS</h1>
                <h2 class="mt-2 text-xl text-gray-600">Sign in to your account</h2>
            </div>

            <!-- Login Form -->
            <div class="bg-white py-8 px-6 rounded-lg shadow-md">
                <?php if (!empty($error)): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                        <?= $this->e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/login" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            autofocus
                            value="<?= $this->e($old['email'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="admin@phpcms.local"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••"
                        />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            name="remember_me"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Sign in
                        </button>
                    </div>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center text-sm">
                    <a href="/forgot-password" class="text-blue-600 hover:text-blue-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-600">
                <p>Default credentials: <code class="bg-gray-200 px-2 py-1 rounded">admin@phpcms.local</code> / <code class="bg-gray-200 px-2 py-1 rounded">admin123</code></p>
            </div>
        </div>
    </div>
</body>
</html>
