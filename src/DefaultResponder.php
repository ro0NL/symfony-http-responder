<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class DefaultResponder implements Responder
{
    public function respond(Respond $respond): Response
    {
        if ($respond instanceof RespondRaw) {
            return new Response($respond->contents, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondRedirect) {
            return new RedirectResponse($respond->url, $respond->status, $respond->headers);
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}
