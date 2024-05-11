<?php

namespace TimeLogger\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotAllowed
{
    public function index(): JsonResponse
    {
        return new JsonResponse(
            ['error' => 'Method Not Allowed'],
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }
}
