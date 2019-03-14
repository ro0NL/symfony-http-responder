<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Respond\NoContent;
use ro0NL\HttpResponder\Respond\Raw;
use ro0NL\HttpResponder\Respond\Redirect;
use ro0NL\HttpResponder\Respond\Stream;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class DefaultResponder extends ProvidingResponder
{
    protected function getProviders(): iterable
    {
        yield Raw::class => static function (Raw $respond): Response {
            return new Response($respond->contents);
        };

        yield Redirect::class => static function (Redirect $respond): Response {
            return new RedirectResponse($respond->url);
        };

        yield NoContent::class => static function (NoContent $respond): Response {
            return new Response('');
        };

        yield Stream::class => static function (Stream $respond): Response {
            return new StreamedResponse($respond->callback);
        };
    }
}
