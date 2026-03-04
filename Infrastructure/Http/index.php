<?php

declare(strict_types=1);

/**
 * HTTP Foundation Classes
 * 
 * Simplified replacements for Symfony\Component\HttpFoundation
 */

namespace Infrastructure\Http;

// Re-export classes for easy importing
class_alias(Request::class, 'Infrastructure\Http\Request');
class_alias(Response::class, 'Infrastructure\Http\Response');
class_alias(JsonResponse::class, 'Infrastructure\Http\JsonResponse');
class_alias(Session::class, 'Infrastructure\Http\Session');
class_alias(FlashBag::class, 'Infrastructure\Http\FlashBag');
