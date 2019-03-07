<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Routing;

use ro0NL\HttpResponder\AggregatedResponder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RouterResponder extends AggregatedResponder
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    protected function getAggregates(): iterable
    {
        yield RespondRouteRedirect::class => function (RespondRouteRedirect $respond): RedirectResponse {
            return new RedirectResponse($this->urlGenerator->generate($respond->name, $respond->parameters, $respond->referenceType), $respond->status, $respond->headers);
        };
    }
}
