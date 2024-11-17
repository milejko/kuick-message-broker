<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server;

use Throwable;

/**
 * 
 */
class Router
{
    public function __construct(private array $routes = []) {}

    public function execute(Request $request): JsonResponse
    {
        try {
            $route = $this->findRoute($request);
        } catch (Throwable $t) {
            return new JsonErrorResponse($t->getMessage(), 500);
        }
        if (!$route) {
            return new JsonErrorResponse('Route not found', 404);
        }
        //execute guard
        if (isset($route['guard']) && $route['guard']) {
            try {
                (new $route['guard'])->__invoke($request);
            } catch (Throwable $t) {
                return new JsonErrorResponse($t->getMessage(), 401);
            }
        }
        //execute action
        return (new $route['action'])->__invoke($request);
    }

    private function findRoute(Request $request): ?array
    {
        foreach ($this->routes as $route) {
            $this->validateRoute($route);
            if ($request->method != $route['method']) {
                continue;
            }
            if (preg_match('#^' . $route['path'] . '$#', $request->path)) {
                return $route;
            }
        }
        return null;
    }

    private function validateRoute(array $route): void
    {
        if (!isset($route['method'])) {
            throw new RouterException('Router failed: one or more routes are missing method name');
        }
        if (!isset($route['path'])) {
            throw new RouterException('Router failed: one or more routes are missing path');
        }
        if (!isset($route['action'])) {
            throw new RouterException('Router failed: one or more routes are action class name');
        }
        if (!class_exists($route['action'])) {
            throw new RouterException('Router failed: action "' . $route['action'] . '" does not exist');
        }
        if (isset($route['guard']) && '' && $route['guard'] && !class_exists($route['guard'])) {
            throw new RouterException('Router failed: guard "' . $route['guard'] . '" does not exist');
        }
    }
}
