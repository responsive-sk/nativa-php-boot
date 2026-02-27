<?php

declare(strict_types=1);

namespace Interfaces\HTTP;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Frontend\Article\ByTagAction;
use Interfaces\HTTP\Actions\Frontend\Article\ListArticlesAction;
use Interfaces\HTTP\Actions\Frontend\Article\SearchArticlesAction;
use Interfaces\HTTP\Actions\Frontend\Article\ShowArticleAction;
use Interfaces\HTTP\Actions\Frontend\HomeAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP Kernel - Main entry point for handling requests
 */
class Kernel
{
    private Router $router;

    public function __construct()
    {
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

            // Handle Action classes
            if (is_string($callback) && class_exists($callback)) {
                // Add route params to request attributes
                foreach ($params as $key => $value) {
                    $request->attributes->set($key, $value);
                }
                
                $action = $callback::create();
                return $action->handle($request);
            }

            // Convert array-style controller [Class::class, 'method'] to callable
            if (is_array($callback) && count($callback) === 2) {
                $controller = $this->container->get($callback[0]);
                $method = $callback[1];

                // Convert associative array to positional arguments
                // If controller method expects Request, inject it
                $positionalParams = array_values($params);
                
                // Check if method expects Request as additional parameter
                try {
                    $reflection = new \ReflectionMethod($controller, $method);
                    $methodParams = $reflection->getParameters();
                    
                    if (count($methodParams) > count($positionalParams)) {
                        foreach ($methodParams as $index => $param) {
                            $typeName = $param->getType()?->getName();
                            if (!isset($positionalParams[$index]) && str_ends_with($typeName, 'Request')) {
                                array_splice($positionalParams, $index, 0, [$request]);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore reflection errors, use original params
                }
                
                $response = $controller->$method(...$positionalParams);
            } else {
                $response = call_user_func_array($callback, $params);
            }

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
        
        // Legacy routes - keep controllers for not-yet-refactored
        $this->router->get('/page/{slug}', [Frontend\PageController::class, 'show']);
        $this->router->get('/contact', [Frontend\ContactController::class, 'show']);
        $this->router->post('/contact', [Frontend\ContactController::class, 'submit']);
        $this->router->get('/form/{slug}', [Frontend\FormController::class, 'show']);
        $this->router->post('/form/{slug}', [Frontend\FormController::class, 'submit']);

        // Admin Routes
        $this->router->get('/admin', [Admin\DashboardController::class, 'index']);

        // Admin Articles
        $this->router->get('/admin/articles', [Admin\ArticleController::class, 'index']);
        $this->router->get('/admin/articles/create', [Admin\ArticleController::class, 'create']);
        $this->router->post('/admin/articles', [Admin\ArticleController::class, 'store']);
        $this->router->get('/admin/articles/{id}/edit', [Admin\ArticleController::class, 'edit']);
        $this->router->put('/admin/articles/{id}', [Admin\ArticleController::class, 'update']);
        $this->router->delete('/admin/articles/{id}', [Admin\ArticleController::class, 'destroy']);
        $this->router->post('/admin/articles/{id}/publish', [Admin\ArticleController::class, 'publish']);

        // Admin Pages
        $this->router->get('/admin/pages', [Admin\PageController::class, 'index']);
        $this->router->get('/admin/pages/create', [Admin\PageController::class, 'create']);
        $this->router->post('/admin/pages', [Admin\PageController::class, 'store']);
        $this->router->get('/admin/pages/{id}/edit', [Admin\PageController::class, 'edit']);
        $this->router->put('/admin/pages/{id}', [Admin\PageController::class, 'update']);
        $this->router->delete('/admin/pages/{id}', [Admin\PageController::class, 'destroy']);

        // Admin Forms
        $this->router->get('/admin/forms', [Admin\FormController::class, 'index']);
        $this->router->get('/admin/forms/create', [Admin\FormController::class, 'create']);
        $this->router->post('/admin/forms', [Admin\FormController::class, 'store']);
        $this->router->get('/admin/forms/{id}/edit', [Admin\FormController::class, 'edit']);
        $this->router->put('/admin/forms/{id}', [Admin\FormController::class, 'update']);
        $this->router->delete('/admin/forms/{id}', [Admin\FormController::class, 'destroy']);
        $this->router->get('/admin/forms/{id}/submissions', [Admin\FormController::class, 'submissions']);
        
        // Admin Media
        $this->router->get('/admin/media', [Admin\MediaController::class, 'index']);
        $this->router->post('/admin/media/upload', [Admin\MediaController::class, 'upload']);
        $this->router->delete('/admin/media/{id}', [Admin\MediaController::class, 'destroy']);
        
        // Admin Settings
        $this->router->get('/admin/settings', [Admin\SettingsController::class, 'index']);
        $this->router->put('/admin/settings', [Admin\SettingsController::class, 'update']);
    }
}
