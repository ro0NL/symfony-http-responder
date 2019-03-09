<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class AggregatedResponder implements ResponderAggregate
{
    /**
     * @psalm-var array<class-string<Respond>, Responder|false>
     *
     * @var array
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

    /**
     * @inheritdoc
     */
    public function getResponder(string $respondClass): ?Responder
    {
        if (isset($this->cache[$respondClass])) {
            return $this->cache[$respondClass] ?: null;
        }

        foreach ($this->getAggregates() as $class => $callback) {
            if ($class !== $respondClass) {
                continue;
            }

            return $this->cache[$respondClass] = new class($callback) implements Responder {
                /**
                 * @var callable
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
     * @return iterable|callable[]
     */
    abstract protected function getAggregates(): iterable;
}
