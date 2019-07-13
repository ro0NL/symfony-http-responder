<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Respond\AbstractRespond;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class OuterResponder implements Responder
{
    private $responder;
    private $flashBag;

    public function __construct(Responder $responder, FlashBagInterface $flashBag = null)
    {
        $this->responder = $responder;
        $this->flashBag = $flashBag;
    }

    public function respond(Respond $respond): Response
    {
        $response = $this->responder->respond($respond);

        if (!$respond instanceof AbstractRespond) {
            return $response;
        }

        if (null !== $this->flashBag) {
            foreach ($respond->flashes as $type => $messages) {
                foreach ($messages as $message) {
                    $this->flashBag->add($type, $message);
                }
            }
        } elseif ($respond->flashes) {
            throw new \LogicException(sprintf('Respond "%s" cannot have flashes because no flash bag is available.', \get_class($respond)));
        }

        $response->headers->add($respond->headers);

        [$statusCode, $statusText] = $respond->status;
        $response->setStatusCode($statusCode, $statusText);

        if (null !== $respond->date) {
            $response->setDate($respond->date);
        }

        if (null !== $respond->linkProvider) {
            $links = [];
            foreach ($respond->linkProvider->getLinks() as $link) {
                if ($link->isTemplated()) {
                    continue;
                }
                $attributes = ['', sprintf('rel="%s"', implode(' ', $link->getRels()))];
                /** @psalm-suppress MixedAssignment */
                /** @var scalar $value */
                foreach ($link->getAttributes() as $attribute => $value) {
                    if ($attribute === $value) {
                        $attributes[] = $value;
                    } else {
                        $attributes[] = sprintf('%s="%s"', $attribute, (string) $value);
                    }
                }
                $links[] = sprintf('<%s>%s', $link->getHref(), implode('; ', $attributes));
            }
            if ($links) {
                $response->headers->set('link', implode(', ', $links), false);
            }
        }

        return $response;
    }
}
