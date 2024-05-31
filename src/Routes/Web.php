<?php

namespace TimeLogger\Routes;

use FastRoute\RouteCollector;

class Web implements RouteInterface
{
    public static function routes(RouteCollector $r): void
    {
        $r->addRoute('POST', '/projects/create', ['TimeLogger\Controllers\ProjectController','create']);
        $r->addRoute('GET', '/projects/{projectId}/export', ['TimeLogger\Controllers\ProjectController','export']);
        $r->addRoute('GET', '/projects/{projectId}', ['TimeLogger\Controllers\ProjectController','read']);
        $r->addRoute('GET', '/projects/', ['TimeLogger\Controllers\ProjectController','list']);
        $r->addRoute('PUT', '/projects/{projectId}', ['TimeLogger\Controllers\ProjectController','update']);
        $r->addRoute('DELETE', '/projects/{projectId}', ['TimeLogger\Controllers\ProjectController','delete']);

        $r->addRoute('POST', '/projects/{projectId}/tasks/create', ['TimeLogger\Controllers\TaskController','create']);
        $r->addRoute('GET', '/projects/{projectId}/tasks/{taskId}/export', ['TimeLogger\Controllers\TaskController','export']);
        $r->addRoute('GET', '/projects/{projectId}/tasks/{taskId}', ['TimeLogger\Controllers\TaskController','read']);
        $r->addRoute('GET', '/projects/{projectId}/tasks', ['TimeLogger\Controllers\TaskController','list']);
        $r->addRoute('PUT', '/projects/{projectId}/tasks/{taskId}', ['TimeLogger\Controllers\TaskController','update']);
        $r->addRoute('DELETE', '/projects/{projectId}/tasks/{taskId}', ['TimeLogger\Controllers\TaskController','delete']);

        $r->addRoute('POST', '/projects/{projectId}/tasks/{taskId}/bookings/start', ['TimeLogger\Controllers\BookingController','start']);
        $r->addRoute('PUT', '/projects/{projectId}/tasks/{taskId}/bookings/stop', ['TimeLogger\Controllers\BookingController','stop']);
        $r->addRoute('GET', '/projects/{projectId}/tasks/{taskId}/bookings/list', ['TimeLogger\Controllers\BookingController','list']);
        $r->addRoute('GET', '/projects/{projectId}/tasks/{taskId}/bookings/{bookingId}', ['TimeLogger\Controllers\BookingController','read']);
        $r->addRoute('DELETE', '/projects/{projectId}/tasks/{taskId}/bookings/{bookingId}', ['TimeLogger\Controllers\BookingController','delete']);

    }
}
