<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\JsonResponder;
use ro0NL\HttpResponder\Respond\Json;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 */
final class JsonResponderTest extends ResponderTestCase
{
    protected const DEFAULT_RESPONSE_CLASS = JsonResponse::class;

    public function testRespondJson(): void
    {
        $response = $this->doRespond(new Json(['"hello" & <world>™']));

        self::assertResponse($response);
        self::assertSame('["\u0022hello\u0022 \u0026 \u003Cworld\u003E\u2122"]', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonWithPrimitive(): void
    {
        $response = $this->doRespond(new Json('json'));

        self::assertResponse($response);
        self::assertSame('"json"', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonWithEncodingOptions(): void
    {
        $response = $this->doRespond(new Json(['"hello" & <world>™'], 0));

        self::assertResponse($response);
        self::assertSame('["\"hello\" & <world>\u2122"]', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonp(): void
    {
        $response = $this->doRespond((new Json('js'))->withCallback('hello'));

        self::assertResponse($response);
        self::assertSame('/**/hello("js");', $response->getContent());
        self::assertSame('text/javascript', $response->headers->get('content-type'));
    }

    public function testRespondJsonRaw(): void
    {
        $response = $this->doRespond(Json::raw('["\"hello\" & world™"]'));

        self::assertResponse($response);
        self::assertSame('["\"hello\" & world™"]', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonRawWithEncodingOptions(): void
    {
        $response = $this->doRespond(Json::raw('["\"hello\" & world™"]', \JSON_HEX_AMP));

        self::assertResponse($response);
        self::assertSame('["\"hello\" \u0026 world\u2122"]', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonRawFromInvalidState(): void
    {
        $responder = $this->getResponder();
        $respond = Json::raw('json');
        $respond->data = 1;

        $this->expectException(\LogicException::class);

        $responder->respond($respond);
    }

    public function testRespondJsonRawFromBrokenString(): void
    {
        $response = $this->doRespond(Json::raw('{broken'));

        self::assertResponse($response);
        self::assertSame('{broken', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    public function testRespondJsonRawFromBrokenStringWithEncodingOptions(): void
    {
        $response = $this->doRespond(Json::raw('{broken', 0));

        self::assertResponse($response);
        self::assertSame('null', $response->getContent());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }

    protected function getResponder(): Responder
    {
        return new JsonResponder();
    }

    protected function getResponds(): iterable
    {
        yield new Json(null);
        yield Json::raw('');
    }
}
