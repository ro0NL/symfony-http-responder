<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\OuterResponder;
use ro0NL\HttpResponder\Respond\AbstractRespond;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class OuterResponderTest extends ResponderTestCase
{
    protected const IS_CATCH_ALL_RESPONDER = true;

    public function testRespond(): void
    {
        $response = $this->doRespond($respond = $this->createMock(Respond::class));

        self::assertSame(\get_class($respond), $response->getContent());
        self::assertSame([], $this->getFlashBag()->all());
    }

    public function testRespondWithFlashWithThrowingResponder(): void
    {
        $responder = new OuterResponder($this->getThrowingResponder(), $this->getFlashBag());

        try {
            $responder->respond($this->getMockForAbstractClass(AbstractRespond::class)->withFlash('type', 'ignored'));
            self::fail();
        } catch (BadRespondTypeException $e) {
            self::assertSame([], $this->getFlashBag()->all());
        }
    }

    public function testRespondWithFlashesWithoutFlashBag(): void
    {
        $responder = new OuterResponder($this->getResponder());

        $this->expectException(\LogicException::class);

        $responder->respond($this->getMockForAbstractClass(AbstractRespond::class)->withFlashes(['type' => 'unsupported']));
    }

    protected function getResponder(): Responder
    {
        return new OuterResponder(new class() implements Responder {
            public function respond(Respond $respond): Response
            {
                return new Response(\get_class($respond));
            }
        }, $this->getFlashBag());
    }

    protected function getResponds(): iterable
    {
        yield $this->createMock(Respond::class);
        yield $this->getMockForAbstractClass(AbstractRespond::class);
    }
}
