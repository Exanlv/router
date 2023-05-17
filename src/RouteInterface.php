<?php

namespace Exan\Router;

use Exan\Router\Route\ResolvedRoute;
use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function matches(ServerRequestInterface $request): bool;
    public function resolve(ServerRequestInterface $request): ResolvedRoute;
}
