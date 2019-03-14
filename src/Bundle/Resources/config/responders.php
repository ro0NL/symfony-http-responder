<?php

declare(strict_types=1);

use ro0NL\HttpResponder\Bridge\Routing\RouterResponder;
use ro0NL\HttpResponder\Bridge\Twig\TwigResponder;
use ro0NL\HttpResponder\DefaultResponder;
use ro0NL\HttpResponder\FileResponder;
use ro0NL\HttpResponder\JsonResponder;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()
            ->autowire()
            ->instanceof(Responder::class)
                ->tag('http_responder', ['priority' => -1024])

        // core responders
        ->set('.http_responder.default', DefaultResponder::class)
        ->set('.http_responder.file', FileResponder::class)
        ->set('.http_responder.json', JsonResponder::class)

        // bridge responders
        ->set('.http_responder.twig', TwigResponder::class)
        ->set('.http_responder.routing', RouterResponder::class)
    ;
};
