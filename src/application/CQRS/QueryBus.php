<?php

declare(strict_types=1);

namespace Application\CQRS;

/**
 * Query Bus - Dispatches queries to handlers
 */
class QueryBus
{
    /** @var array<class-string, callable> */
    private array $handlers = [];

    /**
     * Register a query handler
     *
     * @param class-string $queryClass
     */
    public function register(string $queryClass, callable $handler): void
    {
        $this->handlers[$queryClass] = $handler;
    }

    /**
     * Dispatch a query to its handler
     *
     * @template T
     * @param QueryInterface $query
     * @return T
     */
    public function dispatch(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);

        if (!isset($this->handlers[$queryClass])) {
            throw new \RuntimeException("No handler registered for query: {$queryClass}");
        }

        return ($this->handlers[$queryClass])($query);
    }
}
