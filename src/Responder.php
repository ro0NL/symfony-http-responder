<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
interface Responder
{
    public function respond(Respond $respond): Response;
}