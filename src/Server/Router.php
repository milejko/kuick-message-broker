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
    private const VALID_METHODS = [
        Request::METHOD_GET,
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_PATCH,
        Request::METHOD_DELETE,
        Request::METHOD_HEAD,
        Request::METHOD_OPTIONS,
    ];

    public function __construct(private array $routes = []) {}

    public function execute(Request $request): JsonResponse
    {
        try {
            $route = $this->findRoute($request);
            if (!$route) {
                throw new RouterException('Not found');
            }
            //execute guards
            foreach ($route['guards'] as $guardName) {
                $guard = new $guardName;
                if (!($guard instanceof Guard)) {
                    throw new RouterException('Router failed: invalid ' . $route['guard'] . ' implementation');
                }
                $guard->__invoke($request);
            }
            //execute filters
            foreach ($route['filters'] as $filterName) {
                $filter = new $filterName;
                if (!($filter instanceof Filter)) {
                    throw new RouterException('Router failed: invalid ' . $route['filter'] . ' implementation');
                }
                $filter->__invoke($request);
            }
            $action = new $route['action'];
            if (!($action instanceof Action)) {
                throw new RouterException('Router failed: invalid ' . $route['action'] . ' implementation');
            }
            //execute action
            return $action->__invoke($request);
        } catch(RouterException $error) {
            return new JsonNotFoundResponse($error);
        } catch (GuardException $error) {
            return new JsonUnauthorizedResponse($error);
        } catch (FilterException $error) {
            return new JsonBadRequestResponse($error);
        } catch(ActionException $error) {
            return new JsonNotFoundResponse($error);
        } catch (Throwable $error) {
            return new JsonErrorResponse($error);
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
        if (!isset($route['method']) || !in_array($route['method'], self::VALID_METHODS)) {
            throw new RouterException('Router failed: method missing or invalid');
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
        if (!isset($route['guards'])) {
            throw new RouterException('Router failed: guards missing');
        }
        if (!is_array($route['guards'])) {
            throw new RouterException('Router failed: guards malformed - not an array');
        }
        if (!isset($route['guards'])) {
            throw new RouterException('Router failed: filters missing');
        }
        if (!is_array($route['guards'])) {
            throw new RouterException('Router failed: filters malformed - not an array');
        }
        foreach ($route['guards'] as $guard) {
            if (!class_exists($guard)) {
                throw new RouterException('Router failed: guard class "' . $guard . '" does not exist');        
            }
        }
    }
}
