<?php

namespace Exan\Router;

use Exan\Router\Exceptions\HttpNotFoundException;
use Exan\Router\Exceptions\HttpNotImplementedException;
use Exan\Router\Route\ResolvedRoute;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use ReflectionParameter;

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
                try {
                    $resolvedRoute = $route->resolve($request);

                    $this->resolve($resolvedRoute, $request);
                } catch (HttpNotFoundException) {
                    continue;
                }
                return;
            }
        }

        throw new HttpNotFoundException();
    }

    private function resolve(ResolvedRoute $resolvedRoute, ServerRequestInterface $request)
    {
        if (!$this->container->has($resolvedRoute->controller)) {
            throw new HttpNotImplementedException();
        }

        $controller = $this->container->get($resolvedRoute->controller);

        $reflectionMethod = new ReflectionMethod(
            $controller,
            $resolvedRoute->method,
        );

        if (!$reflectionMethod->isPublic()) {
            throw new HttpNotImplementedException();
        }

        $controller->{$resolvedRoute->method}(
            ...$this->buildArgs(
                $reflectionMethod->getParameters(),
                $resolvedRoute->resolvedParameters,
                $request,
            )
        );
    }

    /**
     * @param ReflectionParameter[] $reflectionParameters
     */
    private function buildArgs(
        array $reflectionParameters,
        array $resolvedParameters,
        ServerRequestInterface $request
    ): array {
        $args = [];

        foreach ($reflectionParameters as $parameter) {
            $parameterName = $parameter->getName();

            if ($parameterName === 'request') {
                $args[] = $request;
                continue;
            }

            if (!isset($resolvedParameters[$parameterName])) {
                throw new HttpNotImplementedException();
            }

            $args[] = $this->formArg($parameter, $resolvedParameters[$parameterName]);
        }

        return $args;
    }

    private function formArg(ReflectionParameter $parameter, string $rawValue): mixed
    {
        if (!$parameter->hasType()) {
            return $rawValue;
        }

        $type = $parameter->getType()->getName();

        switch ($type) {
            case 'string':
                return $rawValue;
            case 'int':
                return (int) $rawValue;
            case 'bool':
                return (bool) $rawValue;
            case 'float':
                return (float) $rawValue;
        }

        if (!in_array(BuildFromRoute::class, class_implements($type))) {
            throw new HttpNotImplementedException();
        }

        /** @var BuildFromRoute $type */
        return $type::buildFromRoute($rawValue);
    }
}
