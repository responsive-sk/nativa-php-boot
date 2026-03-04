<?php

declare(strict_types = 1);

namespace Infrastructure\Http;

/**
 * JSON Response class.
 *
 * Replaces Symfony\Component\HttpFoundation\JsonResponse
 */
class JsonResponse extends Response
{
    /**
     * Create JSON response.
     *
     * @param array<string, mixed> $data
     */
    public function __construct(
        array $data = [],
        int $statusCode = 200,
        array $headers = []
    ) {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        parent::__construct($content, $statusCode, $headers);
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Create JSON response from data.
     *
     * @param array<string, mixed> $data
     */
    public static function create(array $data, int $statusCode = 200): self
    {
        return new self($data, $statusCode);
    }

    /**
     * Create JSON error response.
     */
    #[\Override]
    public static function error(string $message, int $statusCode = 400): self
    {
        return new self([
            'error'   => true,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Create JSON success response.
     *
     * @param array<string, mixed> $data
     */
    public static function success(array $data = []): self
    {
        return new self([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
