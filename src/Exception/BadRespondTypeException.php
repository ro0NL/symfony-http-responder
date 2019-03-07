<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Exception;

use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class BadRespondTypeException extends \LogicException
{
    public static function create(Responder $responder, Respond $respond): self
    {
        return new self(sprintf('The responder "%s" is unable to handle respond type "%s".', \get_class($responder), \get_class($respond)));
    }
}
