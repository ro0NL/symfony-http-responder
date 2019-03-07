<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class DefaultResponder extends AggregatedResponder
{
    protected function getAggregates(): iterable
    {
        yield RespondRaw::class => function (RespondRaw $respond): Response {
            return new Response($respond->contents, $respond->status, $respond->headers);
        };

        yield RespondRedirect::class => function (RespondRedirect $respond): Response {
            return new RedirectResponse($respond->url, $respond->status, $respond->headers);
        };

        yield RespondEmpty::class => function (RespondEmpty $respond): Response {
            return new Response('', $respond->status, $respond->headers);
        };
    }
}
