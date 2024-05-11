<?php

namespace TimeLogger\Routes;

use FastRoute\RouteCollector;

interface RouteInterface
{
    public static function routes(RouteCollector $r): void;
}
