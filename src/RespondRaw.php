<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RespondRaw extends Respond
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
