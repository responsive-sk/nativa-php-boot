<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions;

use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Infrastructure\Http\JsonResponse;

/**
 * Abstract Base Action
 */
abstract class Action implements ActionInterface
{
    /**
     * Get request data
     */
    protected function get(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->getRequestParam($key, $request->getQueryParam($key, $default));
    }

    /**
     * Get route parameter
     */
    protected function param(Request $request, string $key, mixed $default = null): mixed
    {
        // Check if param was added directly to attributes (new way)
        if ($request->attributes($key) !== null) {
            return $request->attributes($key);
        }

        // Fallback to _route_params (old way)
        $attributes = $request->attributes();
        return $attributes['_route_params'][$key] ?? $default;
    }

    /**
     * Create JSON response
     *
     * @psalm-param array<string, mixed> $data
     */
    protected function json(array $data, int $status = 200): Response
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Create redirect response
     */
    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    /**
     * Create HTML response
     */
    protected function html(string $content, int $status = 200): Response
    {
        return Response::html($content, $status);
    }

    /**
     * Create error response
     */
    protected function error(string $message, int $status = 400): Response
    {
        return Response::error($message, $status);
    }

    /**
     * Create not found response
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return Response::notFound($message);
    }
}
