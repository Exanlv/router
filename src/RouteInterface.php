<?php

namespace Exan\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function matches(ServerRequestInterface $request): bool;
    public function resolve(ServerRequestInterface $request);
}
