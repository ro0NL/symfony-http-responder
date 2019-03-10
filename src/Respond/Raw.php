<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Raw extends AbstractRespond
{
    /**
     * @var string
     */
    public $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }
}
