<?php

namespace Exan\Router\Route;

class ResolvedRoute
{
    /**
     * @param class-string $controller
     */
    public function __construct(
        public readonly string $controller,
        public readonly string $method,
        public readonly array $resolvedParameters,
    ) {
    }
}
