<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\DefaultResponder;
use ro0NL\HttpResponder\Respond\NoContent;
use ro0NL\HttpResponder\Respond\Raw;
use ro0NL\HttpResponder\Respond\Redirect;
use ro0NL\HttpResponder\Respond\Stream;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DefaultResponderTest extends ResponderTestCase
{
    public function testRespondRaw(): void
    {
        $response = $this->doRespond(new Raw('"hello" & <world>™'));

        self::assertResponse($response);
        self::assertSame('"hello" & <world>™', $response->getContent());
    }

    public function testRespondRedirect(): void
    {
        $response = $this->doRespond(new Redirect('/path'));

        self::assertResponse($response, 302);
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/path', $response->headers->get('location'));
    }

    public function testRespondNoContent(): void
    {
        $response = $this->doRespond(new NoContent());

        self::assertResponse($response, 204);
        self::assertSame('', $response->getContent());
    }

    public function testRespondStream(): void
    {
        $response = $this->doRespond(new Stream(function (): void {
            echo 'hello stream';
        }));

        self::assertResponse($response);
        self::assertInstanceOf(StreamedResponse::class, $response);

        $this->expectOutputString('hello stream');

        $response->sendContent();
    }

    public function testRespondStreamFromIterable(): void
    {
        $response = $this->doRespond(Stream::iterable(['hello', ' ', 'stream']));

        self::assertResponse($response);
        self::assertInstanceOf(StreamedResponse::class, $response);

        $this->expectOutputString('hello stream');

        $response->sendContent();
    }

    protected function getResponder(): Responder
    {
        return new DefaultResponder();
    }

    protected function getResponds(): iterable
    {
        yield new Raw('contents');
        yield new Redirect('/path');
        yield new NoContent();
        yield new Stream(function (): void {
        });
        yield Stream::iterable([]);
    }
}
