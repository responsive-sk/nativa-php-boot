<?php

declare(strict_types=1);

namespace Interfaces\HTTP;

use Interfaces\HTTP\Actions\Frontend\HomeAction;
use Interfaces\HTTP\Actions\Frontend\Article\ListArticlesAction;
use Interfaces\HTTP\Actions\Frontend\Article\ShowArticleAction;
use Interfaces\HTTP\Actions\Frontend\Article\ByTagAction;
use Interfaces\HTTP\Actions\Frontend\Article\SearchArticlesAction;
use Interfaces\HTTP\Actions\Frontend\ContactAction;
use Interfaces\HTTP\Actions\Frontend\DisplayFormAction;
use Interfaces\HTTP\Actions\Frontend\DisplayPageAction;
use Interfaces\HTTP\Actions\Admin\DashboardAction;
use Interfaces\HTTP\Actions\Admin\FormsAction;
use Interfaces\HTTP\Actions\Admin\CreateFormAction;
use Interfaces\HTTP\Actions\Admin\EditFormAction;
use Interfaces\HTTP\Actions\Admin\ArticlesAction;
use Interfaces\HTTP\Actions\Admin\FormSubmissionsAction;
use Interfaces\HTTP\Actions\Admin\MediaAction;
use Interfaces\HTTP\Actions\Admin\PagesAction;
use Interfaces\HTTP\Actions\Admin\CreatePageAction;
use Interfaces\HTTP\Actions\Admin\EditPageAction;
use Interfaces\HTTP\Actions\Admin\DeletePageAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP Kernel - Main entry point for handling requests
 */
class Kernel
{
    private Router $router;
    private \Infrastructure\Container\Container $container;

    public function __construct()
    {
        $this->container = \Infrastructure\Container\ContainerFactory::create();
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function handle(Request $request): Response
    {
        try {
            // Support method override via _method parameter (POST body, query, or header)
            $originalMethod = $request->getMethod();
            $method = $originalMethod;

            if ($originalMethod === 'POST') {
                $overrideMethod = $request->request->get('_method')
                    ?? $request->query->get('_method')
                    ?? $request->headers->get('X-Http-Method-Override');
                if ($overrideMethod) {
                    $method = strtoupper($overrideMethod);
                }
            }

            // Temporarily set the method for routing
            $request->setMethod($method);
            $route = $this->router->match($request);

            // Restore original method for controller access if needed
            $request->setMethod($originalMethod);

            if ($route === null) {
                return new Response('Not Found', 404);
            }

            $callback = $route['callback'];
            $params = $route['params'];

            // Handle Action classes (string or Class@method format)
            if (is_string($callback)) {
                // Add route params to request attributes
                foreach ($params as $key => $value) {
                    $request->attributes->set($key, $value);
                }
                
                // Also check for _route_id from router.php (for direct URL access)
                if (!$request->attributes->has('id') && $request->query->has('_route_id')) {
                    $request->attributes->set('id', $request->query->get('_route_id'));
                }

                // Handle Class@method format
                if (str_contains($callback, '@')) {
                    [$className, $methodName] = explode('@', $callback);
                    if (class_exists($className)) {
                        $action = $className::create();
                        return $action->$methodName($request);
                    }
                }

                // Handle Action class directly
                if (class_exists($callback)) {
                    $action = $callback::create();
                    return $action->handle($request);
                }
            }

            // Handle array-style controller callbacks [Class::class, 'method']
            if (is_array($callback) && count($callback) === 2) {
                $controller = $this->container->get($callback[0]);
                $method = $callback[1];
                return $controller->$method($request, ...array_values($params));
            }

            // Fallback for any other callbacks
            $response = call_user_func_array($callback, $params);

            if ($response instanceof Response) {
                return $response;
            }

            if (is_array($response) || is_string($response)) {
                return new Response(json_encode($response), 200, ['Content-Type' => 'application/json']);
            }

            return new Response((string) $response);
        } catch (\Throwable $e) {
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                return new Response(
                    '<pre>' . htmlspecialchars($e->getMessage()) . "\n\n" . $e->getTraceAsString() . '</pre>',
                    500
                );
            }
            return new Response('Internal Server Error', 500);
        }
    }

    public function terminate(Request $request, Response $response): void
    {
        // Cleanup
    }

    private function registerRoutes(): void
    {
        // Frontend Routes - Actions pattern
        $this->router->get('/', HomeAction::class);
        $this->router->get('/articles', ListArticlesAction::class);
        $this->router->get('/articles/{slug}', ShowArticleAction::class);
        $this->router->get('/tag/{slug}', ByTagAction::class);
        $this->router->get('/search', SearchArticlesAction::class);

        // Contact Form - Action pattern
        $this->router->get('/contact', ContactAction::class);
        $this->router->post('/contact', ContactAction::class);

        // Form Builder - Frontend
        $this->router->get('/form/{slug}', DisplayFormAction::class);
        $this->router->post('/form/{slug}', DisplayFormAction::class);

        // Admin Routes - MUST BE BEFORE /{slug} (catch-all)!
        $this->router->get('/admin', DashboardAction::class);

        // Admin Forms
        $this->router->get('/admin/forms', FormsAction::class);
        $this->router->get('/admin/forms/create', CreateFormAction::class);
        $this->router->post('/admin/forms/create', CreateFormAction::class);
        $this->router->get('/admin/forms/{id}/edit', EditFormAction::class);
        $this->router->post('/admin/forms/{id}/edit', EditFormAction::class);
        $this->router->get('/admin/forms/{id}/submissions', FormSubmissionsAction::class);

        // Admin Articles - Actions
        $this->router->get('/admin/articles', ArticlesAction::class);

        // TODO: Convert to Actions
        // $this->router->get('/admin/articles/create', [ArticleController::class, 'create']);
        // $this->router->post('/admin/articles', [ArticleController::class, 'store']);
        // $this->router->get('/admin/articles/{id}/edit', [ArticleController::class, 'edit']);
        // $this->router->put('/admin/articles/{id}', [ArticleController::class, 'update']);
        // $this->router->delete('/admin/articles/{id}', [ArticleController::class, 'destroy']);
        // $this->router->post('/admin/articles/{id}/publish', [ArticleController::class, 'publish']);

        // Admin Pages - Actions (CRUD Complete!)
        $this->router->get('/admin/pages', PagesAction::class);
        $this->router->get('/admin/pages/create', CreatePageAction::class);
        $this->router->post('/admin/pages', CreatePageAction::class);
        $this->router->get('/admin/pages/{id}/edit', EditPageAction::class);
        $this->router->post('/admin/pages/{id}/edit', EditPageAction::class);
        $this->router->delete('/admin/pages/{id}', DeletePageAction::class);

        // Admin Media - Action
        $this->router->get('/admin/media', MediaAction::class);
        $this->router->post('/admin/media', MediaAction::class);
        // TODO: Delete action
        // $this->router->delete('/admin/media/{id}', [MediaController::class, 'destroy']);

        // Admin Settings - TODO: Convert to Action
        // $this->router->get('/admin/settings', [SettingsController::class, 'index']);
        // $this->router->put('/admin/settings', [SettingsController::class, 'update']);

        // Static Pages - MUST BE LAST (catch-all for /slug)
        $this->router->get('/{slug}', DisplayPageAction::class);
    }
}
