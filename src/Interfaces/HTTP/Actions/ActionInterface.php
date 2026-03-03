<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions;

use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Base Action Interface.
 */
interface ActionInterface
{
    /**
     * Handle the request and return a response.
     */
    public function handle(Request $request): Response;
}
