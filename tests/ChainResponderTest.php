<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\ChainResponder;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\ProvidingResponder;
use ro0NL\HttpResponder\Respond\AbstractRespond;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class ChainResponderTest extends ResponderTestCase
{
    public function testRespond(): void
    {
        $response = $this->doRespond(new TestRespondA());

        self::assertSame('A', $response->getContent());
    }

    public function testRespondFromAggregate(): void
    {
        $response = $this->doRespond(new TestRespondB());

        self::assertSame('B', $response->getContent());
    }

    protected function getResponder(iterable $responders = []): Responder
    {
        return new ChainResponder([
            new TestChainedResponder(),
            new TestChainedResponderAggregate(),
        ]);
    }

    protected function getResponds(): iterable
    {
        yield new TestRespondA();
        yield new TestRespondB();
    }
}

final class TestChainedResponder implements Responder
{
    public function respond(Respond $respond): Response
    {
        if ($respond instanceof TestRespondA) {
            return new Response('A');
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}

final class TestChainedResponderAggregate extends ProvidingResponder
{
    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function getProviders(): iterable
    {
        yield TestRespondA::class => static function (TestRespondA $respond): Response {
            throw new \LogicException('Should not happen.');
        };
        yield TestRespondB::class => static function (TestRespondB $respond): Response {
            return new Response('B');
        };
        yield TestRespondB::class => static function (TestRespondB $respond): Response {
            throw new \LogicException('Should not happen.');
        };
    }
}

final class TestRespondA implements Respond
{
}

final class TestRespondB extends AbstractRespond
{
}
