<?php

declare(strict_types = 1);

namespace Infrastructure\Http;

/**
 * HTTP Response class.
 *
 * Replaces Symfony\Component\HttpFoundation\Response
 */
class Response
{
    private string $content = '';

    private int $statusCode = 200;

    /** @var array<string, string> */
    private array $headers = [];

    /**
     * Create response.
     */
    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Send response to browser.
     */
    public function send(): void
    {
        // Send status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send content
        echo $this->content;
        flush();
    }

    /**
     * Get content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set content.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set status code.
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Get header.
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Set header.
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Get all headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set all headers.
     *
     * @param array<string, string> $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Create HTML response.
     */
    public static function html(string $content, int $statusCode = 200): self
    {
        $response = new self($content, $statusCode);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');

        return $response;
    }

    /**
     * Create JSON response.
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $response = new self($content, $statusCode);
        $response->setHeader('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }

    /**
     * Create redirect response.
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->setHeader('Location', $url);

        return $response;
    }

    /**
     * Create not found response.
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return new self($message, 404);
    }

    /**
     * Create error response.
     */
    public static function error(string $message, int $statusCode = 500): self
    {
        return new self($message, $statusCode);
    }

    /**
     * Clear cookie (set expiration to past).
     */
    public function clearCookie(
        string $name,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true
    ): self {
        // Set cookie with expiration in the past
        setcookie($name, '', time() - 3600, $path, $domain, $secure, $httpOnly);

        // Also remove from headers array if present
        unset($this->headers['Set-Cookie']);

        return $this;
    }
}
