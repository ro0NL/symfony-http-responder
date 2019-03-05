<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Twig;

use ro0NL\HttpResponder\Respond;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RespondTemplate extends Respond
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $context;

    public function __construct(string $name, array $context = [])
    {
        $this->name = $name;
        $this->context = $context;
    }
}
