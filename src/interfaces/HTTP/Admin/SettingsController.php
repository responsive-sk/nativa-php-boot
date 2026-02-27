<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    public function index(): Response
    {
        return new Response('Settings');
    }

    public function update(): Response
    {
        return new Response('Update Settings');
    }
}
