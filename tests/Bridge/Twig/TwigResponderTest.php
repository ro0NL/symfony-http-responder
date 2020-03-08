<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests\Bridge\Twig;

use ro0NL\HttpResponder\Bridge\Twig\Template;
use ro0NL\HttpResponder\Bridge\Twig\TwigResponder;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
final class TwigResponderTest extends ResponderTestCase
{
    public function testRespond(): void
    {
        $response = $this->doRespond(new Template('default'));

        self::assertResponse($response);
        self::assertSame('hello twig', $response->getContent());
    }

    public function testRespondWithContext(): void
    {
        $response = $this->doRespond(new Template('template', ['name' => 'symfony']));

        self::assertResponse($response);
        self::assertSame('hello symfony', $response->getContent());
    }

    public function testRespondStream(): void
    {
        $response = $this->doRespond((new Template('template', ['name' => 'stream']))->stream());

        self::assertResponse($response);
        self::assertInstanceOf(StreamedResponse::class, $response);

        $this->expectOutputString('hello stream');

        $response->sendContent();
    }

    public function testRespondWithUnknownTemplate(): void
    {
        $responder = $this->getResponder();

        $this->expectException(LoaderError::class);

        $responder->respond(new Template('unknown'));
    }

    protected function getResponder(array $templates = []): Responder
    {
        return new TwigResponder(new Environment(new ArrayLoader($templates + [
            'default' => 'hello twig',
            'template' => 'hello {{ name }}',
        ])));
    }

    protected function getResponds(): iterable
    {
        yield new Template('default');
    }
}
