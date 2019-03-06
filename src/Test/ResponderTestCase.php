<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Test;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ResponderTestCase extends TestCase
{
    protected const DEFAULT_RESPONSE_CLASS = Response::class;
    protected const DEFAULT_RESPONSE_STATUS = 200;

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
}
