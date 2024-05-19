<?php

namespace TimeLogger;

use DI\Container;
use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use TimeLogger\Routes\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function FastRoute\simpleDispatcher;

class App
{
    /**
     * @var App
     */
    private static $instance;

    private Dispatcher $dispatcher;

    private Container $container;

    private function __construct()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '/di-config.php');
        $this->container = $builder->build();

        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            Web::routes($r);
        });
    }

    public static function getInstance(): App
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @throws \Exception
     */
    public function route(Request $request): Response
    {
        $uri = $request->getRequestUri();
        $httpMethod = $request->getMethod();

        if (!GateKeeper::canAccess($request)) {
            return $this->container->call(['\TimeLogger\Controllers\NotAuthenticated','index']);
        }

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->container->call(['\TimeLogger\Controllers\NotFound','index']);
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                return $this->container->call(['\TimeLogger\Controllers\NotAllowed','index'], [$allowedMethods]);
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $parameters = $routeInfo[2];
                return $this->container->call($handler, $parameters);
        }

        throw new \Exception('Dispatcher error');
    }
}
