<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests\Bundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Bundle\DependencyInjection\Extension;
use ro0NL\HttpResponder\ChainResponder;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\ProvidingResponder;
use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class ExtensionTest extends TestCase
{
    public function testExtension(): void
    {
        $container = $this->createContainer();

        /** @var Definition $decorator */
        $decoratorRef = $container->getDefinition(TestService::class)->getArgument(0);
        self::assertInstanceOf(Reference::class, $decoratorRef);
        self::assertSame(TestDecoratingResponder::class, (string) $decoratorRef);

        /** @var Definition $responder */
        $decorator = $container->findDefinition((string) $decoratorRef);
        /** @var Definition $responder */
        $responder = $decorator->getArgument(0);

        self::assertSame(ChainResponder::class, $responder->getClass());

        /** @var IteratorArgument $responders */
        $responders = $responder->getArgument(0);
        self::assertInstanceOf(IteratorArgument::class, $responders);
        self::assertSame([
            TestResponder::class,
            TestProvidingResponder::class,
            '.http_responder.default',
            '.http_responder.file',
            '.http_responder.json',
            '.http_responder.twig',
            '.http_responder.routing',
        ], array_map(function (Reference $ref): string {
            return (string) $ref;
        }, $responders->getValues()));

        self::assertInstanceOf(TestService::class, $container->get(TestService::class));
    }

    public function testExtensionAlias(): void
    {
        self::assertSame('http_responder', (new Extension())->getAlias());
    }

    public function testExtensionConfig(): void
    {
        self::assertNull((new Extension())->getConfiguration([], new ContainerBuilder()));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new Extension());
        $container->prependExtensionConfig('http_responder', []);

        $container->register(TestResponder::class)
            ->addTag('http_responder')
        ;
        $container->register(TestProvidingResponder::class)
            ->addTag('http_responder')
        ;
        $container->register(TestDecoratingResponder::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setDecoratedService(Responder::class)
            ->setArgument('$responder', new Reference(TestDecoratingResponder::class.'.inner'))
        ;
        $container->register(TestService::class)
            ->setAutowired(true)
            ->setPublic(true)
        ;

        $container->register(Environment::class)
            ->setSynthetic(true)
        ;
        $container->register(UrlGeneratorInterface::class)
            ->setSynthetic(true)
        ;

        $container->compile();

        $container->set(Environment::class, new Environment(new ArrayLoader([])));
        $container->set(UrlGeneratorInterface::class, $this->createMock(UrlGeneratorInterface::class));

        return $container;
    }
}

class TestResponder implements Responder
{
    public function respond(Respond $respond): Response
    {
        throw BadRespondTypeException::create($this, $respond);
    }
}

class TestProvidingResponder extends ProvidingResponder
{
    protected function getProviders(): iterable
    {
        yield from [];
    }
}

class TestDecoratingResponder implements Responder
{
    public function __construct(Responder $responder)
    {
        $responder;
    }

    public function respond(Respond $respond): Response
    {
        throw BadRespondTypeException::create($this, $respond);
    }
}

class TestService
{
    public function __construct(Responder $responder)
    {
        $responder;
    }
}
