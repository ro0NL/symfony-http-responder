<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

/**
 * A responder aggregates responds via another Responder, but is also a Responder itself. This means
 * ResponderAggregate::respond() MUST be forwarded to ResponderAggregate::getResponder()->respond(), or throw in case
 * of NULL.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
interface ResponderAggregate extends Responder
{
    /**
     * @psalm-param class-string<Respond\Respond> $respondClass
     */
    public function getResponder(string $respondClass): ?Responder;
}
