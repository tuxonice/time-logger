<?php

namespace TimeLogger\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotAuthenticated
{
    public function index(): JsonResponse
    {
        return new JsonResponse(
            ['error' => 'Not authenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
