<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Test;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\OuterResponder;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ResponderTestCase extends TestCase
{
    protected const DEFAULT_RESPONSE_CLASS = Response::class;
    protected const DEFAULT_RESPONSE_STATUS = 200;

    public function testRespondWithStatus(): void
    {
        foreach ($this->getResponds() as $respond) {
            $response = $this->doRespond($respond->withStatus(1 + $prevStatus = $respond->status[0]));

            self::assertSame(1 + $prevStatus, $response->getStatusCode());
        }
    }

    public function testRespondWithInvalidStatus(): void
    {
        $responder = new OuterResponder($this->getResponder());

        foreach ($this->getResponds() as $respond) {
            try {
                $responder->respond($respond->withStatus(999));
                self::fail();
            } catch (\LogicException $e) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testRespondWithStatusText(): void
    {
        foreach ($this->getResponds() as $respond) {
            $response = $this->doRespond($respond->withStatus(201, 'Hello HTTP'));

            self::assertSame(201, $response->getStatusCode());
            self::assertStringStartsWith("HTTP/1.0 201 Hello HTTP\r\n", (string) $response);
        }
    }

    public function testRespondWithHeaders(): void
    {
        foreach ($this->getResponds() as $respond) {
            $response = $this->doRespond($respond->withHeaders([
                'h1' => 'v',
                'H2' => ['v1', 'V2'],
            ]));
            $headers = $response->headers->allPreserveCase();

            self::assertArrayHasKey('h1', $headers);
            self::assertSame(['v'], $headers['h1']);
            self::assertArrayHasKey('H2', $headers);
            self::assertSame(['v1', 'V2'], $headers['H2']);
        }
    }

    public function testRespondWithHeader(): void
    {
        foreach ($this->getResponds() as $respond) {
            $response = $this->doRespond($respond
                ->withHeader('h1', 'v')
                ->withHeader('H2', 'ignored')
                ->withHeader('H2', ['v1', 'V2']));
            $headers = $response->headers->allPreserveCase();

            self::assertArrayHasKey('h1', $headers);
            self::assertSame(['v'], $headers['h1']);
            self::assertArrayHasKey('H2', $headers);
            self::assertSame(['v1', 'V2'], $headers['H2']);
        }
    }

    public function testRespondWithFlashes(): void
    {
        foreach ($this->getResponds() as $respond) {
            $flashBag = new FlashBag();

            (new OuterResponder($this->getResponder(), $flashBag))->respond($respond->withFlashes(['type1' => 'X', 'TYPE2' => ['y', true, []]]));

            self::assertSame(['type1' => ['X'], 'TYPE2' => ['y', true, []]], $flashBag->all());
        }
    }

    public function testRespondWithFlashesWithoutFlashBag(): void
    {
        $responder = new OuterResponder($this->getResponder());

        $this->expectException(\LogicException::class);

        $responder->respond($this->getMockForAbstractClass(Respond::class)->withFlashes(['unsupported']));
    }

    public function testRespondWithFlash(): void
    {
        foreach ($this->getResponds() as $respond) {
            $flashBag = new FlashBag();

            (new OuterResponder($this->getResponder(), $flashBag))->respond($respond
                ->withFlash('type1', 'X')
                ->withFlash('TYPE2', 'not ignored')
                ->withFlash('TYPE2', ['y', true, []]));

            self::assertSame(['type1' => ['X'], 'TYPE2' => ['not ignored', 'y', true, []]], $flashBag->all());
        }
    }

    public function testUnknownRespond(): void
    {
        $responder = $this->getResponder();

        $this->expectException(BadRespondTypeException::class);

        $responder->respond($this->getMockForAbstractClass(Respond::class));
    }

    protected static function assertResponse(Response $response, int $status = null): void
    {
        if (Response::class !== static::DEFAULT_RESPONSE_CLASS) {
            self::assertInstanceOf(static::DEFAULT_RESPONSE_CLASS, $response);
        }

        self::assertSame($status ?? static::DEFAULT_RESPONSE_STATUS, $response->getStatusCode());
    }

    abstract protected function getResponder(): Responder;

    /**
     * @return iterable|Respond[]
     */
    abstract protected function getResponds(): iterable;

    protected function doRespond(Respond $respond): Response
    {
        return (new OuterResponder($this->getResponder()))->respond($respond);
    }
}
