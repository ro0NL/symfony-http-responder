<?php

declare(strict_types=1);

use ro0NL\HttpResponder\Bridge\Routing\RouterResponder;
use ro0NL\HttpResponder\Bridge\Twig\TwigResponder;
use ro0NL\HttpResponder\ChainResponder;
use ro0NL\HttpResponder\DefaultResponder;
use ro0NL\HttpResponder\FileResponder;
use ro0NL\HttpResponder\JsonResponder;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged;

return function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
            ->autoconfigure()
            ->autowire()

        // core responders
        ->set('.http_responder.default', DefaultResponder::class)
        ->set('.http_responder.file', FileResponder::class)
        ->set('.http_responder.json', JsonResponder::class)
        ->set('.http_responder.twig', JsonResponder::class)

        // bridge responders
        ->set('.http_responder.routing', RouterResponder::class)
        ->set('.http_responder.twig', TwigResponder::class)

        // main responder
        ->set('.http_responder.main', ChainResponder::class)
            ->autoconfigure(false)
            ->arg('$responders', tagged('http_responder'))
        ->alias(Responder::class, '.http_responder.main')

        // decorators
        // ...
    ;
};
