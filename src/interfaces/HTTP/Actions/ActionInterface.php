<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base Action Interface
 */
interface ActionInterface
{
    /**
     * Handle the request and return a response
     */
    public function handle(Request $request): Response;
}
