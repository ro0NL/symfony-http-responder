<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;

/**
 * A providing responder uses pure callable factories mapped to Respond types.
 *
 * {@inheritdoc}
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ProvidingResponder implements ResponderAggregate
{
    /**
     * @var array<class-string<Respond>, Responder|false>
     */
    private $cache = [];

    final public function respond(Respond $respond): Response
    {
        $responder = $this->getResponder(\get_class($respond));

        if (null === $responder) {
            throw BadRespondTypeException::create($this, $respond);
        }

        return $responder->respond($respond);
    }

    public function getResponder(string $respondClass): ?Responder
    {
        if (isset($this->cache[$respondClass])) {
            return $this->cache[$respondClass] ?: null;
        }

        foreach ($this->getProviders() as $class => $callback) {
            if ($class !== $respondClass) {
                continue;
            }

            return $this->cache[$respondClass] = new class($callback) implements Responder {
                /**
                 * @var callable(Respond):Response
                 */
                private $callback;

                public function __construct(callable $callback)
                {
                    $this->callback = $callback;
                }

                public function respond(Respond $respond): Response
                {
                    return ($this->callback)($respond);
                }
            };
        }

        $this->cache[$respondClass] = false;

        return null;
    }

    /**
     * @return iterable<class-string<Respond>, callable(Respond):Response>
     */
    abstract protected function getProviders(): iterable;
}
