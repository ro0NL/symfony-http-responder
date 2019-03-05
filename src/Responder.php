<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
interface Responder
{
    /**
     * @throws BadRespondTypeException when the respond type cannot be handled by this responder
     */
    public function respond(Respond $respond): Response;
}
