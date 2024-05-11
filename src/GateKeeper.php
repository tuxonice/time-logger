<?php

namespace TimeLogger;

use Symfony\Component\HttpFoundation\Request;

class GateKeeper
{
    public static function canAccess(Request $request): bool
    {
        $bearerToken = $request->headers->get('X-Bearer-Token');

        $authorizationToken = $_ENV['AUTHORIZATION_TOKEN'];

        if ($bearerToken !== null && hash_equals($authorizationToken, $bearerToken)) {
            return true;
        }

        return false;
    }
}
