<?php

namespace Exan\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * @param RouteInterface[] $routes
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array $routes,
    ) {
    }

    public function run(ServerRequestInterface $request)
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                $this->resolve($route, $request);
                return;
            }
        }
    }

    private function resolve(RouteInterface $route, ServerRequestInterface $request)
    {
    }
}
