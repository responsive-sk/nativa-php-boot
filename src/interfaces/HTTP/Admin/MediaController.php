<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Admin;

use Symfony\Component\HttpFoundation\Response;

class MediaController
{
    public function index(): Response
    {
        return new Response('Media Library');
    }

    public function upload(): Response
    {
        return new Response('Upload');
    }

    public function destroy(): Response
    {
        return new Response('Delete');
    }
}
