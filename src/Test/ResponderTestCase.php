<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Test;

use Fig\Link\Link;
use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\OuterResponder;
use ro0NL\HttpResponder\Respond\AbstractRespond;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ResponderTestCase extends TestCase
{
    protected const DEFAULT_RESPONSE_CLASS = Response::class;
    protected const DEFAULT_RESPONSE_STATUS = 200;
    protected const IS_CATCH_ALL_RESPONDER = false;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    protected function tearDown(): void
    {
        $this->getFlashBag()->clear();
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithStatus(AbstractRespond $respond): void
    {
        $response = $this->doRespond($respond->withStatus(1 + $prevStatus = $respond->status[0]));

        self::assertSame(1 + $prevStatus, $response->getStatusCode());
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithInvalidStatus(AbstractRespond $respond): void
    {
        $responder = $this->getOuterResponder();

        try {
            $responder->respond($respond->withStatus(999));
            self::fail();
        } catch (\LogicException $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithStatusText(AbstractRespond $respond): void
    {
        $response = $this->doRespond($respond->withStatus(201, 'Hello HTTP'));

        self::assertSame(201, $response->getStatusCode());
        self::assertStringStartsWith("HTTP/1.0 201 Hello HTTP\r\n", (string) $response);
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithDate(AbstractRespond $respond): void
    {
        $response = $this->doRespond($respond->withDate($date = new \DateTime('yesterday')));

        self::assertInstanceOf(\DateTime::class, $response->getDate());
        /** @psalm-suppress PossiblyNullReference */
        self::assertSame($date->getTimestamp(), $response->getDate()->getTimestamp());
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithoutDate(AbstractRespond $respond): void
    {
        $response = $this->doRespond($respond);

        self::assertInstanceOf(\DateTime::class, $date = $response->getDate());
        self::assertTrue($date > new \DateTime('yesterday'));
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithHeaders(AbstractRespond $respond): void
    {
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

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithHeader(AbstractRespond $respond): void
    {
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

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithFlashes(AbstractRespond $respond): void
    {
        $this->getOuterResponder()->respond($respond->withFlashes(['type1' => 'X', 'TYPE2' => ['y', true, 1]]));

        self::assertSame(['type1' => ['X'], 'TYPE2' => ['y', true, 1]], $this->getFlashBag()->all());
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithFlash(AbstractRespond $respond): void
    {
        $this->getOuterResponder()->respond($respond
            ->withFlash('type1', 'X')
            ->withFlash('TYPE2', 'not ignored')
            ->withFlash('TYPE2', ['y', true, 1]));

        self::assertSame(['type1' => ['X'], 'TYPE2' => ['not ignored', 'y', true, 1]], $this->getFlashBag()->all());
    }

    /**
     * @dataProvider provideAbstractResponds
     */
    public function testRespondWithLink(AbstractRespond $respond): void
    {
        if (!class_exists(Link::class)) {
            self::markTestSkipped('Missing "fig/link-util".');
        }

        $response = $this->getOuterResponder()->respond($respond
            ->withHeader('link', 'custom')
            ->withLink('href')
            ->withLink('href{templated}')
            ->withLink('href2', ['rel', 'rel2'])
            ->withLink('href3', ['rel'], ['a' => true, 'b', 'c' => 'foo bar', 'd' => false, 'e' => 'boo', 'f' => 'f']));

        self::assertSame(['custom', '<href>; rel="", <href2>; rel="rel rel2", <href3>; rel="rel"; a; b; c="foo bar"; e="boo"; f'], $response->headers->get('link', null, false));
    }

    public function testRespondUnknown(): void
    {
        $responder = $this->getResponder();

        if (static::IS_CATCH_ALL_RESPONDER) {
            $responder->respond($this->createMock(Respond::class));

            $this->addToAssertionCount(1);

            return;
        }

        $this->expectException(BadRespondTypeException::class);

        $responder->respond($this->createMock(Respond::class));
    }

    public function provideResponds(): iterable
    {
        foreach ($this->getResponds() as $respond) {
            yield [$respond];
        }
    }

    public function provideAbstractResponds(): iterable
    {
        foreach ($this->getResponds() as $respond) {
            if ($respond instanceof AbstractRespond) {
                yield [$respond];
            }
        }
    }

    protected static function assertResponse(Response $response, int $status = null): void
    {
        /** @var class-string $class */
        $class = static::DEFAULT_RESPONSE_CLASS;
        if (Response::class !== $class) {
            self::assertInstanceOf($class, $response);
        }

        self::assertSame($status ?? static::DEFAULT_RESPONSE_STATUS, $response->getStatusCode());
    }

    abstract protected function getResponder(): Responder;

    /**
     * @return iterable<int, Respond>
     */
    abstract protected function getResponds(): iterable;

    protected function getThrowingResponder(): Responder
    {
        return new class() implements Responder {
            public function respond(Respond $respond): Response
            {
                throw BadRespondTypeException::create($this, $respond);
            }
        };
    }

    protected function getOuterResponder(): OuterResponder
    {
        $responder = $this->getResponder();

        return $responder instanceof OuterResponder ? $responder : new OuterResponder($responder, $this->getFlashBag());
    }

    protected function getFlashBag(): FlashBagInterface
    {
        return $this->flashBag ?? ($this->flashBag = new FlashBag());
    }

    protected function doRespond(Respond $respond): Response
    {
        return $this->getOuterResponder()->respond($respond);
    }
}
