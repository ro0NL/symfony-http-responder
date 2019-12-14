<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class ChainResponder implements Responder
{
    /** @var iterable<int, Responder> */
    private $responders;

    /**
     * @param iterable<int, Responder> $responders
     */
    public function __construct(iterable $responders)
    {
        $this->responders = $responders;
    }

    public function respond(Respond $respond): Response
    {
        $class = \get_class($respond);

        foreach ($this->responders as $responder) {
            if ($responder instanceof ResponderAggregate) {
                $aggregated = $responder->getResponder($class);

                if (null === $aggregated) {
                    continue;
                }

                return $aggregated->respond($respond);
            }

            try {
                return $responder->respond($respond);
            } catch (BadRespondTypeException $e) {
            }
        }

        throw BadRespondTypeException::create($this, $respond);
    }
}
