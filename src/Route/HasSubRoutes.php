<?php

namespace Exan\Router\Route;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @property RouteInterface[] $routes
 */
trait HasSubRoutes
{
    public function resolve(ServerRequestInterface $request)
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $route;
            }
        }
    }
}
