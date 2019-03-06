<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Routing;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RouterResponder implements Responder
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function respond(Respond $respond): Response
    {
        if ($respond instanceof RespondRouteRedirect) {
            return new RedirectResponse($this->urlGenerator->generate($respond->name, $respond->parameters, $respond->referenceType), $respond->status, $respond->headers);
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}
