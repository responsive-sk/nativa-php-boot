<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions;

use Infrastructure\Http\JsonResponse;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Abstract Base Action.
 *
 * Provides common helper methods for all action controllers.
 */
abstract class Action implements ActionInterface
{
    /**
     * Get request data (POST or GET).
     */
    protected function get(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->getRequestParam($key, $request->getQueryParam($key, $default));
    }

    /**
     * Get route parameter.
     */
    protected function param(Request $request, string $key, mixed $default = null): mixed
    {
        if (null !== $request->attributes($key)) {
            return $request->attributes($key);
        }

        $attributes = $request->attributes();

        return $attributes['_route_params'][$key] ?? $default;
    }

    /**
     * Check if request method is GET.
     */
    protected function isGet(Request $request): bool
    {
        return 'GET' === $request->getMethod();
    }

    /**
     * Check if request method is POST.
     */
    protected function isPost(Request $request): bool
    {
        return 'POST' === $request->getMethod();
    }

    /**
     * Check if request method is PUT or PATCH.
     */
    protected function isPutOrPatch(Request $request): bool
    {
        $method = $request->getMethod();

        return 'PUT' === $method || 'PATCH' === $method;
    }

    /**
     * Check if request method is DELETE.
     */
    protected function isDelete(Request $request): bool
    {
        return 'DELETE' === $request->getMethod();
    }

    /**
     * Create JSON response.
     *
     * @param array<string, mixed> $data
     */
    protected function json(array $data, int $status = 200): Response
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Create redirect response.
     */
    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    /**
     * Create HTML response.
     */
    protected function html(string $content, int $status = 200): Response
    {
        return Response::html($content, $status);
    }

    /**
     * Create error response.
     */
    protected function error(string $message, int $status = 400): Response
    {
        return Response::error($message, $status);
    }

    /**
     * Create not found response.
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return Response::notFound($message);
    }

    /**
     * Render page template with layout.
     *
     * @param array<string, mixed> $data
     */
    protected function renderPage(
        Request $request,
        TemplateRenderer $renderer,
        string $template,
        array $data = [],
        ?string $layout = 'frontend',
        int $status = 200
    ): Response {
        $content = $renderer->render($template, $data, $layout);

        return $this->html($content, $status);
    }
}
