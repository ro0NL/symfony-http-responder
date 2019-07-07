<?php

declare(strict_types=1);

use ro0NL\HttpResponder\ChainResponder;
use ro0NL\HttpResponder\OuterResponder;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    /** @psalm-suppress DeprecatedFunction */
    $container->services()
        ->defaults()
            ->private()
            ->autowire()

        // main responder
        ->set('http_responder', OuterResponder::class)
            ->arg('$responder', inline(ChainResponder::class)
                ->arg('$responders', function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator') ? tagged_iterator('http_responder') : tagged('http_responder')))
        ->alias(Responder::class, 'http_responder')
    ;
};
