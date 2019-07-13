<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Twig;

use ro0NL\HttpResponder\Respond\AbstractRespond;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Template extends AbstractRespond
{
    public $name;
    public $context;
    /** @var bool */
    public $stream = false;

    public function __construct(string $name, array $context = [])
    {
        $this->name = $name;
        $this->context = $context;
    }

    /**
     * @return $this
     */
    public function stream(): self
    {
        $this->stream = true;

        return $this;
    }
}
