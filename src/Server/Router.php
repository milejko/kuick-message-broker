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
            if (!$route) {
                throw new RouterException('Not found');
            }
            //execute guard
            if (isset($route['guard']) && $route['guard']) {
                $guard = new $route['guard'];
                if (!($guard instanceof Guard)) {
                    throw new RouterException('Router failed: invalid ' . $route['guard'] . ' implementation');
                }
                $guard->__invoke($request);
            }
            $action = new $route['action'];
            if (!($action instanceof Action)) {
                throw new RouterException('Router failed: invalid ' . $route['action'] . ' implementation');
            }
            //execute action
            return $action->__invoke($request);
        } catch(RouterException $error) {
            return new JsonErrorResponse($error->getMessage(), JsonResponse::CODE_NOT_FOUND);
        } catch (GuardException $error) {
            return new JsonErrorResponse($error->getMessage(), JsonResponse::CODE_UNAUTHORIZED);
        } catch(ActionException $error) {
            return new JsonErrorResponse($error->getMessage(), JsonResponse::CODE_BAD_REQUEST);
        } catch (Throwable $error) {
            return new JsonErrorResponse($error->getMessage(), JsonResponse::CODE_ERROR);
        }
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
