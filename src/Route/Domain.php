<?php

namespace Exan\Router\Route;

use Exan\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

class Domain implements RouteInterface
{
    use HasSubRoutes;

    public function __construct(
        public string $domain,
        public array $routes,
    ) {
    }

    public function matches(ServerRequestInterface $request): bool
    {
        return in_array($this->domain, $request->getHeader('Host'));
    }
}
