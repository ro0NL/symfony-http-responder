<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

/**
 * An aggregated responder responds via another Responder, but is also a Responder itself. This means
 * respond() MUST be forwarded to getResponder()->respond(), or throw in case of NULL.
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
