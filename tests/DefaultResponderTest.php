<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\DefaultResponder;
use ro0NL\HttpResponder\RespondEmpty;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\RespondRaw;
use ro0NL\HttpResponder\RespondRedirect;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class DefaultResponderTest extends ResponderTestCase
{
    public function testRespondRaw(): void
    {
        $responder = $this->getResponder();
        $response = $responder->respond(new RespondRaw('"hello" <world>™'));

        self::assertResponse($response);
        self::assertSame('"hello" <world>™', $response->getContent());
    }

    public function testRespondRedirect(): void
    {
        $responder = $this->getResponder();
        $response = $responder->respond(new RespondRedirect('/path'));

        self::assertResponse($response, 302);
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/path', $response->headers->get('location'));
    }

    public function testRespondEmpty(): void
    {
        $responder = $this->getResponder();
        $response = $responder->respond(new RespondEmpty());

        self::assertResponse($response, 204);
        self::assertSame('', $response->getContent());
    }

    protected function getResponder(): Responder
    {
        return new DefaultResponder();
    }

    protected function getResponds(): iterable
    {
        yield new RespondRaw('contents');
        yield new RespondRedirect('/path');
        yield new RespondEmpty();
    }
}
