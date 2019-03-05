<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Twig;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class TwigResponder implements Responder
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function respond(Respond $respond): Response
    {
        if ($respond instanceof RespondTemplate) {
            return new Response($this->twig->render($respond->name, $respond->context), $respond->status, $respond->headers);
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}
