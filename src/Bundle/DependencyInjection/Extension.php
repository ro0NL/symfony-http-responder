<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bundle\DependencyInjection;

use ro0NL\HttpResponder\ProvidingResponder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 *
 * @internal
 */
final class Extension extends BaseExtension
{
    public function getAlias(): string
    {
        return 'http_responder';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');
        $loader->load('responders.xml');

        $container->registerForAutoconfiguration(ProvidingResponder::class)
            ->addTag('http_responder')
        ;

        if (!interface_exists(UrlGeneratorInterface::class)) {
            $container->removeDefinition('.http_responder.routing');
        }

        if (!class_exists(Environment::class)) {
            $container->removeDefinition('.http_responder.twig');
        }
    }
}
