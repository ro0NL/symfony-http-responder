<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Twig;

use ro0NL\HttpResponder\ProvidingResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class TwigResponder extends ProvidingResponder
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function getProviders(): iterable
    {
        yield Template::class => function (Template $respond): Response {
            if ($respond->stream) {
                return new StreamedResponse(function () use ($respond): void {
                    $this->twig->display($respond->name, $respond->context);
                });
            }

            return new Response($this->twig->render($respond->name, $respond->context));
        };
    }
}
