<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests\Bridge\Twig;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Bridge\Twig\RespondTemplate;
use ro0NL\HttpResponder\Bridge\Twig\TwigResponder;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TwigResponderTest extends TestCase
{
    public function testRespond(): void
    {
        $responder = new TwigResponder(new Environment(new ArrayLoader([
            'template' => 'hello twig',
        ])));
        $response = $responder->respond(new RespondTemplate('template'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('hello twig', $response->getContent());
    }

    public function testRespondWithContext(): void
    {
        $responder = new TwigResponder(new Environment(new ArrayLoader([
            'template' => 'hello {{ name }}',
        ])));
        $response = $responder->respond(new RespondTemplate('template', ['name' => 'symfony']));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('hello symfony', $response->getContent());
    }

    public function testUnknownRespond(): void
    {
        $responder = new TwigResponder(new Environment(new ArrayLoader()));

        self::expectException(BadRespondTypeException::class);

        $responder->respond($this->getMockForAbstractClass(Respond::class));
    }
}
