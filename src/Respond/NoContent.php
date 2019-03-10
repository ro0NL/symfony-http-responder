<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class NoContent extends AbstractRespond
{
    public function __construct()
    {
        $this->status[0] = Response::HTTP_NO_CONTENT;
    }
}
