<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use ReflectionClass;
use ReflectionParameter;

/**
 * Lightweight Dependency Injection Container with auto-wiring
 */
class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    /** @var array<string, callable|string> */
    private array $bindings = [];

    /** @var array<string, bool> */
    private array $singletons = [];

    /**
     * Bind a class or interface to a concrete implementation
     *
     * @param string $abstract
     * @param callable|string $concrete
     * @param bool $singleton
     */
    public function bind(string $abstract, callable|string $concrete, bool $singleton = false): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = $singleton;

        // If singleton and already resolved, clear the instance
        if ($singleton && isset($this->instances[$abstract])) {
            unset($this->instances[$abstract]);
        }
    }

    /**
     * Bind as singleton (shared instance)
     *
     * @param string $abstract
     * @param callable|string $concrete
     */
    public function singleton(string $abstract, callable|string $concrete): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance
     *
     * @param string $abstract
     * @param object $instance
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
        $this->singletons[$abstract] = true;
    }

    /**
     * Resolve a class or interface
     *
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    public function get(string $abstract): object
    {
        // Return existing instance if singleton
        if (isset($this->instances[$abstract]) && $this->singletons[$abstract]) {
            return $this->instances[$abstract];
        }

        // Check for binding
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];

            if (is_callable($concrete)) {
                $instance = $concrete($this);
            } else {
                $instance = $this->build($concrete);
            }
        } else {
            // Auto-wire the class
            $instance = $this->build($abstract);
        }

        // Store if singleton
        if ($this->singletons[$abstract] ?? false) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a class with dependency injection
     *
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private function build(string $class): object
    {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve constructor dependencies
     *
     * @param array<ReflectionParameter> $parameters
     * @return array<mixed>
     */
    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                // No type hint, check for default value
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException(
                        "Cannot resolve parameter '{$parameter->getName()}' - no type hint or default value"
                    );
                }
                continue;
            }

            $typeName = $type->getName();

            // Skip built-in types (they should have default values)
            if (in_array($typeName, ['string', 'int', 'bool', 'float', 'array', 'callable'])) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException(
                        "Cannot resolve primitive parameter '{$parameter->getName()}' of type '{$typeName}'"
                    );
                }
                continue;
            }

            // Resolve class dependency
            try {
                $dependencies[] = $this->get($typeName);
            } catch (ContainerException $e) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw $e;
                }
            }
        }

        return $dependencies;
    }

    /**
     * Check if a class is bound or resolved
     */
    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * Remove a binding or instance
     */
    public function forget(string $abstract): void
    {
        unset($this->bindings[$abstract], $this->instances[$abstract], $this->singletons[$abstract]);
    }

    /**
     * Build a class without caching (always new instance)
     *
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function make(string $class): object
    {
        return $this->build($class);
    }

    /**
     * Call a method with dependency injection
     *
     * @param object|array{class-string, string} $callback
     * @param array<string, mixed> $parameters
     * @return mixed
     */
    public function call(object|array $callback, array $parameters = []): mixed
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;
            $instance = is_object($class) ? $class : $this->get($class);
            $callback = [$instance, $method];
        }

        $reflector = new \ReflectionMethod($callback);
        $dependencies = $this->resolveMethodDependencies($reflector, $parameters);

        return $reflector->invokeArgs($callback, $dependencies);
    }

    /**
     * Resolve method dependencies
     *
     * @param \ReflectionMethod $reflector
     * @param array<string, mixed> $parameters
     * @return array<mixed>
     */
    private function resolveMethodDependencies(\ReflectionMethod $reflector, array $parameters): array
    {
        $dependencies = [];

        foreach ($reflector->getParameters() as $parameter) {
            $name = $parameter->getName();

            // Use provided parameter if available
            if (array_key_exists($name, $parameters)) {
                $dependencies[] = $parameters[$name];
                continue;
            }

            $type = $parameter->getType();

            if ($type === null || in_array($type->getName(), ['string', 'int', 'bool', 'float', 'array', 'callable'])) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException(
                        "Cannot resolve parameter '{$name}' for method {$reflector->getName()}"
                    );
                }
                continue;
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $dependencies;
    }
}
