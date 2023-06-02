<?php

namespace Tests\Exan\Router;

use Exan\Router\Route\Domain;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class DomainTest extends TestCase
{
    /**
     * @dataProvider domainProvider
     */
    public function testItMatchesBasedOnDomain(string $domain, string $domainToMatch, bool $matches): void
    {
        $domainRoute = new Domain($domain, []);

        /** @var ServerRequestInterface&MockInterface */
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->expects()
            ->getHeader()
            ->with('Host')
            ->andReturns([$domainToMatch]);

        self::assertEquals($matches, $domainRoute->matches($request));
    }

    public static function domainProvider(): array
    {
        return [
            'domain.com + domain.com' => [
                'domain' => 'domain.com',
                'domainToMatch' => 'domain.com',
                'matches' => true,
            ],
            'domain.com + otherdomain.com' => [
                'domain' => 'domain.com',
                'domainToMatch' => 'otherdomain.com',
                'matches' => false,
            ],
        ];
    }
}
