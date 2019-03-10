<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Routing;

use ro0NL\HttpResponder\ProvidingResponder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RouterResponder extends ProvidingResponder
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    protected function getProviders(): iterable
    {
        yield RouteRedirect::class => function (RouteRedirect $respond): RedirectResponse {
            return new RedirectResponse($this->urlGenerator->generate($respond->name, $respond->parameters, $respond->referenceType));
        };
    }
}
