<?php

namespace Exan\Router\Route;

use Exan\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

class Group implements RouteInterface
{
    use HasSubRoutes;

    public function __construct(
        private readonly string $pattern,
        private readonly array $routes,
    ) {
    }

    public function matches(ServerRequestInterface $request): bool
    {
        return (bool) preg_match($this->pattern, $request->getUri()->getPath());
    }
}
