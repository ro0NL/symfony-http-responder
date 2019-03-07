<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class NoContent extends Respond
{
    /**
     * @var int
     */
    public $status = Response::HTTP_NO_CONTENT;
}
