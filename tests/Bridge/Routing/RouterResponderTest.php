<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests\Bridge\Routing;

use ro0NL\HttpResponder\Bridge\Routing\RouteRedirect;
use ro0NL\HttpResponder\Bridge\Routing\RouterResponder;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @internal
 */
final class RouterResponderTest extends ResponderTestCase
{
    protected const DEFAULT_RESPONSE_CLASS = RedirectResponse::class;
    protected const DEFAULT_RESPONSE_STATUS = 302;

    public function testRespond(): void
    {
        $response = $this->doRespond(new RouteRedirect('name'));

        self::assertResponse($response);
        self::assertSame('name/1/[]', $response->headers->get('location'));
    }

    public function testRespondWithParameters(): void
    {
        $response = $this->doRespond(new RouteRedirect('name', ['key' => 'value']));

        self::assertResponse($response);
        self::assertSame('name/1/{"key":"value"}', $response->headers->get('location'));
    }

    public function testRespondWithAbsoluteUrl(): void
    {
        $response = $this->doRespond((new RouteRedirect('name'))->withAbsoluteUrl());

        self::assertResponse($response);
        self::assertSame('name/0/[]', $response->headers->get('location'));
    }

    public function testRespondWithRelativePath(): void
    {
        $response = $this->doRespond((new RouteRedirect('name'))->withRelativePath());

        self::assertResponse($response);
        self::assertSame('name/2/[]', $response->headers->get('location'));
    }

    public function testRespondWithNetworkPath(): void
    {
        $response = $this->doRespond((new RouteRedirect('name'))->withNetworkPath());

        self::assertResponse($response);
        self::assertSame('name/3/[]', $response->headers->get('location'));
    }

    protected function getResponder(): Responder
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::any())
            ->method('generate')
            ->willReturnCallback(static function (string $name, array $params, int $type): string {
                return $name.'/'.$type.'/'.json_encode($params);
            })
        ;

        return new RouterResponder($urlGenerator);
    }

    protected function getResponds(): iterable
    {
        yield new RouteRedirect('name');
    }
}
