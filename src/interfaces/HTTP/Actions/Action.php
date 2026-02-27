<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        return $request->request->get($key, $request->query->get($key, $default));
    }

    /**
     * Get route parameter
     */
    protected function param(Request $request, string $key, mixed $default = null): mixed
    {
        // Check if param was added directly to attributes (new way)
        if ($request->attributes->has($key)) {
            return $request->attributes->get($key, $default);
        }
        
        // Fallback to _route_params (old way)
        $attributes = $request->attributes->all();
        return $attributes['_route_params'][$key] ?? $default;
    }

    /**
     * Create JSON response
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        return new Response(
            json_encode($data),
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Create redirect response
     */
    protected function redirect(string $url): Response
    {
        return new Response('', 302, ['Location' => $url]);
    }

    /**
     * Create HTML response
     */
    protected function html(string $content, int $status = 200): Response
    {
        return new Response($content, $status, ['Content-Type' => 'text/html']);
    }

    /**
     * Create error response
     */
    protected function error(string $message, int $status = 400): Response
    {
        return new Response($message, $status);
    }

    /**
     * Create not found response
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return new Response($message, 404);
    }
}
