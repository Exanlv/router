<?php

namespace Exan\Router\Route;

use Exan\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route implements RouteInterface
{
    private $namedParams;

    public function __construct(
        private string $httpMethod,
        private string $pattern,
        private string $controller,
        private string $method,
    ) {
    }

    public function matches(ServerRequestInterface $request): bool
    {
        return $request->getMethod() === $this->httpMethod && $this->matchesPattern($request);
    }

    private function matchesPattern(ServerRequestInterface $request): bool
    {
        $this->namedParams = [];
        $uri = $request->getUri()->getPath();

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

        return true;
    }

    public function resolve(ServerRequestInterface $request): ResolvedRoute
    {
        if (!isset($this->namedParams)) {
            $this->matchesPattern($request);
        }

        return new ResolvedRoute(
            $this->controller,
            $this->method,
            $this->namedParams,
        );
    }
}
