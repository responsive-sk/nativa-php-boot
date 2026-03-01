<?php

declare(strict_types=1);

namespace Infrastructure\Http;

/**
 * Simplified HTTP Request class
 * 
 * Replaces Symfony\Component\HttpFoundation\Request
 * Supports: query params, request body, sessions, method override
 */
final class Request
{
    /** @var array<string, mixed> */
    private array $query = [];

    /** @var array<string, mixed> */
    private array $request = [];

    /** @var array<string, mixed> */
    private array $attributes = [];

    /** @var array<string, string> */
    private array $headers = [];

    private string $method = 'GET';

    private string $pathInfo = '/';

    private ?Session $session = null;

    /**
     * Create request from globals
     */
    public static function createFromGlobals(): self
    {
        $request = new self();

        // Query parameters (?key=value) - cast to array for Psalm
        /** @var array<string, mixed> */
        $query = $_GET;
        $request->query = $query;

        // Request body (POST data) - cast to array for Psalm
        /** @var array<string, mixed> */
        $body = $_POST;
        $request->request = $body;

        // HTTP method
        $request->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Path info (/path/to/page)
        $request->pathInfo = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Headers
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $request->headers[$header] = (string) $value;
            }
        }

        return $request;
    }

    /**
     * Get query parameter (?key=value)
     *
     * @param mixed $default
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get request body parameter (POST data)
     *
     * @param mixed $default
     */
    public function request(string $key, mixed $default = null): mixed
    {
        return $this->request[$key] ?? $default;
    }

    /**
     * Get attribute (route params)
     *
     * @param mixed $default
     */
    public function attributes(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->attributes;
        }
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set attribute (route params)
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get header
     *
     * @param mixed $default
     */
    public function header(string $key, mixed $default = null): ?string
    {
        return $this->headers[strtoupper($key)] ?? $default;
    }

    /**
     * Get all headers
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get header (Symfony compatibility)
     *
     * @param mixed $default
     */
    public function headers(string $key, mixed $default = null): ?string
    {
        return $this->header($key, $default);
    }

    /**
     * Get HTTP method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set HTTP method (for method override)
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * Get path info (/path/to/page)
     */
    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    /**
     * Get or create session
     */
    public function getSession(): Session
    {
        if ($this->session === null) {
            $this->session = new Session();
        }

        return $this->session;
    }

    /**
     * Check if request is AJAX
     */
    public function isXmlHttpRequest(): bool
    {
        return strtolower($this->header('X-Requested-With', '')) === 'xmlhttprequest';
    }

    /**
     * Get JSON body
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        $content = file_get_contents('php://input');
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Get all query parameters
     *
     * @return array<string, mixed>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get all request body parameters
     *
     * @return array<string, mixed>
     */
    public function getRequest(): array
    {
        return $this->request;
    }
}
