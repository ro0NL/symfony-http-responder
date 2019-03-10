<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class OuterResponder implements Responder
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var FlashBagInterface|null
     */
    private $flashBag;

    public function __construct(Responder $responder, FlashBagInterface $flashBag = null)
    {
        $this->responder = $responder;
        $this->flashBag = $flashBag;
    }

    public function respond(Respond $respond): Response
    {
        if (null !== $this->flashBag) {
            foreach ($respond->flashes as $type => $messages) {
                foreach ((array) $messages as $message) {
                    $this->flashBag->add($type, $message);
                }
            }
        } elseif ($respond->flashes) {
            throw new \LogicException(sprintf('Respond "%s" cannot have flashes because no flash bag is available.', \get_class($respond)));
        }

        $response = $this->responder->respond($respond);
        $response->headers->add($respond->headers);

        [$statusCode, $statusText] = $respond->status;
        $response->setStatusCode($statusCode, $statusText);

        if (null !== $respond->date) {
            $response->setDate($respond->date);
        }

        return $response;
    }
}
