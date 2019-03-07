<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Test;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ResponderTestCase extends TestCase
{
    protected const DEFAULT_RESPONSE_CLASS = Response::class;
    protected const DEFAULT_RESPONSE_STATUS = 200;

    public function testRespondWithStatus(): void
    {
        $responder = $this->getResponder();

        foreach ($this->getResponds() as $respond) {
            $response = $responder->respond($respond->withStatus(1 + $prevStatus = $respond->status));

            self::assertSame(1 + $prevStatus, $response->getStatusCode());
        }
    }

    public function testRespondWithInvalidStatus(): void
    {
        $responder = $this->getResponder();

        foreach ($this->getResponds() as $respond) {
            try {
                $responder->respond($respond->withStatus(999));
                self::fail();
            } catch (\LogicException $e) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testRespondWithHeaders(): void
    {
        $responder = $this->getResponder();

        foreach ($this->getResponds() as $respond) {
            $response = $responder->respond($respond->withHeaders([
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
        $responder = $this->getResponder();

        foreach ($this->getResponds() as $respond) {
            $response = $responder->respond($respond->withHeader('h1', 'v')->withHeader('H2', ['v1', 'V2']));
            $headers = $response->headers->allPreserveCase();

            self::assertArrayHasKey('h1', $headers);
            self::assertSame(['v'], $headers['h1']);
            self::assertArrayHasKey('H2', $headers);
            self::assertSame(['v1', 'V2'], $headers['H2']);
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
}
