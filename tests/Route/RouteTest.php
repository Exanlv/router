<?php

namespace Tests\Exan\Router;

use Exan\Router\Route\Route;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouteTest extends TestCase
{
    /**
     * @dataProvider routeProvider
     */
    public function testItMatchesRoutesBasedOnMethodAndPath(string $method, string $pattern, ServerRequestInterface $request, bool $matches): void
    {
        $domainRoute = new Route(
            $method,
            $pattern,
            '\\Not\\A\\Class',
            'something',
        );

        self::assertEquals($matches, $domainRoute->matches($request));
    }

    public static function routeProvider(): array
    {
        return [
            'get /hi/there' => [
                'method' => 'GET',
                'domainToMatch' => '/\/hi\/there/',
                'request' => (function () {
                    /** @var ServerRequestInterface&MockInterface */
                    $request = Mockery::mock(ServerRequestInterface::class);
                    $request->expects()
                        ->getMethod()
                        ->andReturns('GET');

                    /** @var UriInterface&MockInterface */
                    $uri = Mockery::mock(UriInterface::class);
                    $uri->expects()
                        ->getPath()
                        ->andReturns('/hi/there');

                    $request->expects()
                        ->getUri()
                        ->andReturns($uri);

                    return $request;
                })(),
                'matches' => true,
            ],

            'get /users/{id} with id in url' => [
                'method' => 'GET',
                'domainToMatch' => '/\/users\/(?<userId>\d+)/',
                'request' => (function () {
                    /** @var ServerRequestInterface&MockInterface */
                    $request = Mockery::mock(ServerRequestInterface::class);
                    $request->expects()
                        ->getMethod()
                        ->andReturns('GET');

                    /** @var UriInterface&MockInterface */
                    $uri = Mockery::mock(UriInterface::class);
                    $uri->expects()
                        ->getPath()
                        ->andReturns('/users/123');

                    $request->expects()
                        ->getUri()
                        ->andReturns($uri);

                    return $request;
                })(),
                'matches' => true,
            ],

            'get /users/{id} without id in url' => [
                'method' => 'GET',
                'domainToMatch' => '/\/users\/(?<userId>\d+)/',
                'request' => (function () {
                    /** @var ServerRequestInterface&MockInterface */
                    $request = Mockery::mock(ServerRequestInterface::class);
                    $request->expects()
                        ->getMethod()
                        ->andReturns('GET');

                    /** @var UriInterface&MockInterface */
                    $uri = Mockery::mock(UriInterface::class);
                    $uri->expects()
                        ->getPath()
                        ->andReturns('/users/');

                    $request->expects()
                        ->getUri()
                        ->andReturns($uri);

                    return $request;
                })(),
                'matches' => false,
            ],

            'get /hi/there with wrong method' => [
                'method' => 'GET',
                'domainToMatch' => '/\/hi\/there/',
                'request' => (function () {
                    /** @var ServerRequestInterface&MockInterface */
                    $request = Mockery::mock(ServerRequestInterface::class);
                    $request->expects()
                        ->getMethod()
                        ->andReturns('POST');

                    /** @var UriInterface&MockInterface */
                    $uri = Mockery::mock(UriInterface::class);
                    $uri->expects()
                        ->getPath()
                        ->andReturns('/hi/there');

                    $request->expects()
                        ->getUri()
                        ->andReturns($uri);

                    return $request;
                })(),
                'matches' => false,
            ],
        ];
    }

    public function testItResolvesARoute()
    {
        $route = new Route(
            'GET',
            '/\/users\/(?<organizationId>\d+)\/(?<userId>.+)/',
            '\\Not\\A\\Class',
            'something',
        );

        /** @var ServerRequestInterface&MockInterface */
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->expects()
            ->getMethod()
            ->andReturns('GET');

        /** @var UriInterface&MockInterface */
        $uri = Mockery::mock(UriInterface::class);
        $uri->expects()
            ->getPath()
            ->andReturns('/users/123/some-fun-value');

        $request->expects()
            ->getUri()
            ->andReturns($uri);

        $resolved = $route->resolve($request);

        $this->assertEquals([
            'organizationId' => '123',
            'userId' => 'some-fun-value',
        ], $resolved->resolvedParameters);

        $this->assertEquals('\\Not\\A\\Class', $resolved->controller);
        $this->assertEquals('something', $resolved->method);
    }

    public function testItIgnoresUnNamedRegexMatches()
    {
        $route = new Route(
            'GET',
            '/\/users\/(\d+)\/(.+)/',
            '\\Not\\A\\Class',
            'something',
        );

        /** @var ServerRequestInterface&MockInterface */
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->expects()
            ->getMethod()
            ->andReturns('GET');

        /** @var UriInterface&MockInterface */
        $uri = Mockery::mock(UriInterface::class);
        $uri->expects()
            ->getPath()
            ->andReturns('/users/123/some-fun-value');

        $request->expects()
            ->getUri()
            ->andReturns($uri);

        $this->assertTrue($route->matches($request));

        $resolved = $route->resolve($request);

        $this->assertEquals([], $resolved->resolvedParameters);

        $this->assertEquals('\\Not\\A\\Class', $resolved->controller);
        $this->assertEquals('something', $resolved->method);
    }
}
