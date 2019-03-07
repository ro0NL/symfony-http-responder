<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\AggregatedResponder;
use ro0NL\HttpResponder\ChainResponder;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ChainResponderTest extends ResponderTestCase
{
    public function testRespond(): void
    {
        $responder = $this->getResponder();
        $response = $responder->respond(new TestRespondA());

        self::assertSame('A', $response->getContent());
    }

    public function testRespondFromAggregate(): void
    {
        $responder = $this->getResponder();
        $response = $responder->respond(new TestRespondB());

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

/**
 * @internal
 */
final class TestChainedResponder implements Responder
{
    public function respond(Respond $respond): Response
    {
        if ($respond instanceof TestRespondA) {
            return new Response('A', $respond->status, $respond->headers);
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}

/**
 * @internal
 */
final class TestChainedResponderAggregate extends AggregatedResponder
{
    protected function getAggregates(): iterable
    {
        yield TestRespondA::class => function (TestRespondA $respond): Response {
            throw new \LogicException('Should not happen.');
        };

        yield TestRespondB::class => function (TestRespondB $respond): Response {
            return new Response('B', $respond->status, $respond->headers);
        };

        yield TestRespondB::class => function (TestRespondB $respond): Response {
            throw new \LogicException('Should not happen.');
        };
    }
}

/**
 * @internal
 */
final class TestRespondA extends Respond
{
}

/**
 * @internal
 */
final class TestRespondB extends Respond
{
}
