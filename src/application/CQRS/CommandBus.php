<?php

declare(strict_types=1);

namespace Application\CQRS;

/**
 * Command Bus - Dispatches commands to handlers
 */
class CommandBus
{
    /** @var array<class-string, callable> */
    private array $handlers = [];

    /**
     * Register a command handler
     *
     * @param class-string $commandClass
     */
    public function register(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    /**
     * Dispatch a command to its handler
     *
     * @template T of object
     * @param CommandInterface&T $command
     * @return T
     * @throws \RuntimeException If no handler is registered for the command
     */
    public function dispatch(CommandInterface $command): mixed
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException("No handler registered for command: {$commandClass}");
        }

        return ($this->handlers[$commandClass])($command);
    }
}
