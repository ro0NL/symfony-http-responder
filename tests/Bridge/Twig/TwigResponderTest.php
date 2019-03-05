<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests\Bridge\Twig;

use ro0NL\HttpResponder\Bridge\Twig\RespondTemplate;
use ro0NL\HttpResponder\Bridge\Twig\TwigResponder;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;

final class TwigResponderTest extends ResponderTestCase
{
    public function testRespond(): void
    {
        $responder = $this->getResponder([
            'template' => 'hello twig',
        ]);
        $response = $responder->respond(new RespondTemplate('template'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('hello twig', $response->getContent());
    }

    public function testRespondWithContext(): void
    {
        $responder = $this->getResponder([
            'template' => 'hello {{ name }}',
        ]);
        $response = $responder->respond(new RespondTemplate('template', ['name' => 'symfony']));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('hello symfony', $response->getContent());
    }

    public function testRespondWithUnknownTemplate(): void
    {
        $responder = $this->getResponder();

        $this->expectException(LoaderError::class);

        $responder->respond(new RespondTemplate('template'));
    }

    protected function getResponder(array $templates = []): Responder
    {
        return new TwigResponder(new Environment(new ArrayLoader($templates)));
    }
}
