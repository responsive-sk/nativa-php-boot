<?php

declare(strict_types=1);

namespace Interfaces\HTTP;

use Interfaces\HTTP\Actions\Frontend\HomeAction;
use Interfaces\HTTP\Actions\Frontend\BlogAction;
use Interfaces\HTTP\Actions\Frontend\PortfolioAction;
use Interfaces\HTTP\Actions\Frontend\Article\ListArticlesAction;
use Interfaces\HTTP\Actions\Frontend\Article\ListArticlesApiAction;
use Interfaces\HTTP\Actions\Frontend\Article\ShowArticleAction;
use Interfaces\HTTP\Actions\Frontend\Article\ShowBlogArticleAction;
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
use Interfaces\HTTP\Actions\Auth\LoginAction;
use Interfaces\HTTP\Actions\Auth\LogoutAction;
use Interfaces\HTTP\Actions\Admin\Article\CreateArticleAction;
use Interfaces\HTTP\Actions\Admin\Article\StoreArticleAction;
use Interfaces\HTTP\Actions\Admin\Article\EditArticleAction;
use Interfaces\HTTP\Actions\Admin\Article\UpdateArticleAction;
use Interfaces\HTTP\Actions\Admin\Article\DeleteArticleAction;
use Interfaces\HTTP\Actions\Admin\Article\PublishArticleAction;
use Interfaces\HTTP\Actions\Admin\Settings\ViewSettingsAction;
use Interfaces\HTTP\Actions\Admin\Settings\UpdateSettingsAction;
use Interfaces\HTTP\Actions\Admin\Media\DeleteMediaAction;
use Interfaces\HTTP\Actions\Admin\Roles\ListRolesAction;
use Interfaces\HTTP\Actions\Admin\Roles\CreateRoleAction;
use Interfaces\HTTP\Actions\Admin\Roles\EditRoleAction;
use Interfaces\HTTP\Actions\Admin\Permissions\ListPermissionsAction;
use Interfaces\HTTP\Actions\Admin\Permissions\CreatePermissionAction;
use Interfaces\HTTP\Actions\Admin\Permissions\EditPermissionAction;
use Application\Middleware\SecurityHeadersMiddleware;
use Application\Middleware\CsrfMiddleware;
use Application\Middleware\CsrfException;
use Application\Middleware\RateLimitMiddleware;
use Application\Services\RateLimiter;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * HTTP Kernel - Main entry point for handling requests
 */
class Kernel
{
    private Router $router;
    private \Infrastructure\Container\Container $container;
    private SecurityHeadersMiddleware $securityHeadersMiddleware;
    private CsrfMiddleware $csrfMiddleware;
    private RateLimitMiddleware $rateLimitMiddleware;

    public function __construct()
    {
        $this->container = \Infrastructure\Container\ContainerFactory::create();
        $this->router = new Router();
        $this->securityHeadersMiddleware = new SecurityHeadersMiddleware();
        $this->csrfMiddleware = new CsrfMiddleware();
        
        // Initialize Rate Limiter
        $db = \Infrastructure\Persistence\DatabaseConnection::getInstance()->getConnection();
        $rateLimiter = new RateLimiter($db);
        $this->rateLimitMiddleware = new RateLimitMiddleware($rateLimiter);
        
        $this->registerRoutes();
    }

    public function handle(Request $request): Response
    {
        try {
            // Check request size limit (10MB max)
            $contentLengthHeader = $request->headers('Content-Length', '0');
            $contentLength = (int) $contentLengthHeader;
            $maxSize = 10 * 1024 * 1024; // 10MB

            if ($contentLength > $maxSize) {
                $errorResponse = new Response('Payload Too Large: Request exceeds 10MB limit', 413);
                return $this->securityHeadersMiddleware->handle($request, $errorResponse);
            }

            // Apply rate limiting to login requests
            if ($request->getPathInfo() === '/login' && $request->getMethod() === 'POST') {
                $rateLimitResponse = $this->rateLimitMiddleware->limitLogin($request);
                if ($rateLimitResponse !== null) {
                    return $this->securityHeadersMiddleware->handle($request, $rateLimitResponse);
                }
            }

            // Validate CSRF token for state-changing requests
            if ($this->isStateChangingMethod($request->getMethod())) {
                try {
                    $this->csrfMiddleware->validateToken($request);
                } catch (CsrfException $e) {
                    $errorResponse = new Response($this->renderErrorPage('403'), 403, ['Content-Type' => 'text/html']);
                    return $this->securityHeadersMiddleware->handle($request, $errorResponse);
                }
            }

            // Support method override via _method parameter (POST body, query, or header)
            $originalMethod = $request->getMethod();
            $method = $originalMethod;

            if ($originalMethod === 'POST') {
                $overrideMethod = $request->request('_method')
                    ?? $request->query('_method')
                    ?? $request->headers('X-Http-Method-Override');
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
                $response = new Response($this->renderErrorPage('404'), 404, ['Content-Type' => 'text/html']);
                return $this->securityHeadersMiddleware->handle($request, $response);
            }

            // Check authentication for admin routes
            $path = $request->getPathInfo();
            if (str_starts_with($path, '/admin')) {
                // Check auth directly using AuthService (which uses SessionManager internally)
                $authService = $this->container->get(\Application\Services\AuthService::class);
                if (!$authService->check()) {
                    // Initialize session to store intended URL
                    $sessionManager = $this->container->get(\Application\Services\SessionManager::class);
                    $sessionManager->start();
                    $sessionManager->set('intended_url', $path);

                    $response = new Response('', 302, ['Location' => '/login']);
                    return $this->securityHeadersMiddleware->handle($request, $response);
                }
            }

            $callback = $route['callback'];
            $params = $route['params'];

            // Handle Action classes (string or Class@method format)
            if (is_string($callback)) {
                // Add route params to request attributes
                foreach ($params as $key => $value) {
                    $request->setAttribute($key, $value);
                }

                // Also check for _route_id from router.php (for direct URL access)
                $queryParams = $request->getQuery();
                if ($request->attributes('id') === null && isset($queryParams['_route_id'])) {
                    $request->setAttribute('id', $queryParams['_route_id']);
                }

                // Handle Class@method format
                if (str_contains($callback, '@')) {
                    [$className, $methodName] = explode('@', $callback);
                    if (class_exists($className)) {
                        $action = $className::create();
                        $response = $action->$methodName($request);
                        return $this->securityHeadersMiddleware->handle($request, $response);
                    }
                }

                // Handle Action class directly
                if (class_exists($callback)) {
                    $action = $callback::create();
                    $response = $action->handle($request);
                    return $this->securityHeadersMiddleware->handle($request, $response);
                }
            }

            // Handle array-style controller callbacks [Class::class, 'method']
            if (is_array($callback) && count($callback) === 2) {
                $controller = $this->container->get($callback[0]);
                $method = $callback[1];
                $response = $controller->$method($request, ...array_values($params));
                return $this->securityHeadersMiddleware->handle($request, $response);
            }

            // Fallback for any other callbacks
            $response = call_user_func_array($callback, $params);

            if ($response instanceof Response) {
                return $this->securityHeadersMiddleware->handle($request, $response);
            }

            if (is_array($response) || is_string($response)) {
                $jsonResponse = new Response(json_encode($response), 200, ['Content-Type' => 'application/json']);
                return $this->securityHeadersMiddleware->handle($request, $jsonResponse);
            }

            $textResponse = new Response((string) $response);
            return $this->securityHeadersMiddleware->handle($request, $textResponse);
        } catch (\Throwable $e) {
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                $errorResponse = new Response(
                    '<pre>' . htmlspecialchars($e->getMessage()) . "\n\n" . $e->getTraceAsString() . '</pre>',
                    500
                );
                return $this->securityHeadersMiddleware->handle($request, $errorResponse);
            }
            $errorResponse = new Response($this->renderErrorPage('500'), 500, ['Content-Type' => 'text/html']);
            return $this->securityHeadersMiddleware->handle($request, $errorResponse);
        }
    }

    public function terminate(Request $request, Response $response): void
    {
        // Cleanup
    }

    /**
     * Check if HTTP method is state-changing
     */
    private function isStateChangingMethod(string $method): bool
    {
        return in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'], true);
    }

    /**
     * Render error page template
     */
    private function renderErrorPage(string $code): string
    {
        $templatePath = __DIR__ . '/../Templates/frontend/errors/' . $code . '.php';

        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            $result = ob_get_clean();
            return $result !== false ? $result : "Error {$code}";
        }

        return "Error {$code}";
    }

    private function registerRoutes(): void
    {
        // Frontend Routes - Actions pattern
        $this->router->get('/', HomeAction::class);

        // Blog routes (new frontend)
        $this->router->get('/blog', BlogAction::class);
        $this->router->get('/blog/{slug}', ShowBlogArticleAction::class);

        // Portfolio route
        $this->router->get('/portfolio', PortfolioAction::class);
        
        // Contact route
        $this->router->get('/contact', ContactAction::class);
        $this->router->post('/contact', ContactAction::class);
        
        // Legacy article routes (keep for backward compatibility)
        $this->router->get('/articles', ListArticlesAction::class);
        $this->router->get('/articles/{slug}', ShowArticleAction::class);
        $this->router->get('/tag/{slug}', ByTagAction::class);
        $this->router->get('/search', SearchArticlesAction::class);
        
        // API routes for frontend JavaScript
        $this->router->get('/api/articles', ListArticlesApiAction::class);

        // Contact Form - Action pattern
        $this->router->get('/contact', ContactAction::class);
        $this->router->post('/contact', ContactAction::class);

        // Form Builder - Frontend
        $this->router->get('/form/{slug}', DisplayFormAction::class);
        $this->router->post('/form/{slug}', DisplayFormAction::class);

        // Auth Routes
        $this->router->get('/login', [LoginAction::class, 'handle']);
        $this->router->post('/login', [LoginAction::class, 'handle']);
        $this->router->get('/logout', [LogoutAction::class, 'handle']);

        // Admin Routes - MUST BE BEFORE /{slug} (catch-all)!
        $this->router->get('/admin', DashboardAction::class);

        // Admin Forms
        $this->router->get('/admin/forms', FormsAction::class);
        $this->router->get('/admin/forms/create', CreateFormAction::class);
        $this->router->post('/admin/forms/create', CreateFormAction::class);
        $this->router->get('/admin/forms/{id}/edit', EditFormAction::class);
        $this->router->post('/admin/forms/{id}/edit', EditFormAction::class);
        $this->router->get('/admin/forms/{id}/submissions', FormSubmissionsAction::class);

        // Admin Articles - Actions (CRUD Complete!)
        $this->router->get('/admin/articles', ArticlesAction::class);
        $this->router->get('/admin/articles/create', CreateArticleAction::class);
        $this->router->post('/admin/articles', StoreArticleAction::class);
        $this->router->get('/admin/articles/{id}/edit', EditArticleAction::class);
        $this->router->post('/admin/articles/{id}', UpdateArticleAction::class);
        $this->router->delete('/admin/articles/{id}', DeleteArticleAction::class);
        $this->router->post('/admin/articles/{id}/publish', PublishArticleAction::class);

        // Admin Pages - Actions (CRUD Complete!)
        $this->router->get('/admin/pages', PagesAction::class);
        $this->router->get('/admin/pages/create', CreatePageAction::class);
        $this->router->post('/admin/pages', CreatePageAction::class);
        $this->router->get('/admin/pages/{id}/edit', EditPageAction::class);
        $this->router->post('/admin/pages/{id}/edit', EditPageAction::class);
        $this->router->delete('/admin/pages/{id}', DeletePageAction::class);

        // Admin Media - Actions
        $this->router->get('/admin/media', MediaAction::class);
        $this->router->post('/admin/media', MediaAction::class);
        $this->router->delete('/admin/media/{id}', DeleteMediaAction::class);

        // Admin Settings - Actions
        $this->router->get('/admin/settings', ViewSettingsAction::class);
        $this->router->post('/admin/settings', UpdateSettingsAction::class);

        // Admin RBAC - Roles & Permissions
        $this->router->get('/admin/roles', ListRolesAction::class);
        $this->router->get('/admin/roles/create', CreateRoleAction::class);
        $this->router->post('/admin/roles/create', CreateRoleAction::class);
        $this->router->get('/admin/roles/{id}/edit', EditRoleAction::class);
        $this->router->post('/admin/roles/{id}/edit', EditRoleAction::class);
        $this->router->delete('/admin/roles/{id}', EditRoleAction::class);

        $this->router->get('/admin/permissions', ListPermissionsAction::class);
        $this->router->get('/admin/permissions/create', CreatePermissionAction::class);
        $this->router->post('/admin/permissions/create', CreatePermissionAction::class);
        $this->router->get('/admin/permissions/{id}/edit', EditPermissionAction::class);
        $this->router->post('/admin/permissions/{id}/edit', EditPermissionAction::class);
        $this->router->delete('/admin/permissions/{id}', EditPermissionAction::class);

        // Static Pages - MUST BE LAST (catch-all for /slug)
        $this->router->get('/{slug}', DisplayPageAction::class);
    }
}
