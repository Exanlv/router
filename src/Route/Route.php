<?php

namespace Exan\Router\Route;

use Exan\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route implements RouteInterface
{
    private $namedParams = [];

    public function __construct(
        private string $method,
        private string $pattern,
    ) {
    }

    public function matches(ServerRequestInterface $request): bool
    {
        return $request->getMethod() === $this->method && $this->matchesPattern($request->getUri()->getPath());
    }

    private function matchesPattern(string $uri): bool
    {
        $resolvedParams = [];
        $matchesPattern = preg_match($this->pattern, $uri, $resolvedParams);

        if (!$matchesPattern) {
            return false;
        }

        foreach ($resolvedParams as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $this->namedParams[$key] = $value;
        }
    }

    public function resolve(ServerRequestInterface $request)
    {
    }
}
