<?php

namespace TimeLogger\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotFound
{
    public function index(): JsonResponse
    {
        return new JsonResponse(
            ['error' => 'Not Found'],
            Response::HTTP_NOT_FOUND
        );
    }
}
