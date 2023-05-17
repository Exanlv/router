<?php

namespace Exan\Router\Route;

use Exan\Router\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property RouteInterface[] $routes
 */
trait HasSubRoutes
{
    /**
     * @throws HttpNotFoundException
     */
    public function resolve(ServerRequestInterface $request): ResolvedRoute
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                try {
                    return $route->resolve();
                } catch (HttpNotFoundException) {
                    continue;
                }
            }
        }

        throw new HttpNotFoundException();
    }
}
