<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Redirect extends AbstractRespond
{
    /**
     * @var string
     */
    public $url;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->status[0] = Response::HTTP_FOUND;
    }
}
