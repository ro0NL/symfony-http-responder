<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\DefaultResponder;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use ro0NL\HttpResponder\RespondRaw;
use ro0NL\HttpResponder\RespondRedirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class DefaultResponderTest extends TestCase
{
    public function testRespondRaw(): void
    {
        $responder = new DefaultResponder();
        $response = $responder->respond(new RespondRaw('"hello" <world>™'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('"hello" <world>™', $response->getContent());
    }

    public function testRespondRedirect(): void
    {
        $responder = new DefaultResponder();
        $response = $responder->respond(new RespondRedirect('/path'));

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/path', $response->headers->get('location'));
    }

    public function testUnknownRespond(): void
    {
        $responder = new DefaultResponder();

        self::expectException(BadRespondTypeException::class);

        $responder->respond($this->getMockForAbstractClass(Respond::class));
    }
}
