<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;

class HealthCheckController
{
    /**
     * Provides a health check endpoint.
     *
     * @return Response Response is plain text with value "OK"
     */
    public function __invoke(): Response
    {
        return new Response('OK', Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }
}
