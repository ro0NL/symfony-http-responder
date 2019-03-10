<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Stream extends AbstractRespond
{
    /**
     * @var callable
     */
    public $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param iterable|string[] $body
     */
    public static function iterable(iterable $body): self
    {
        return new self(function () use ($body): void {
            foreach ($body as $line) {
                echo $line;
            }
        });
    }
}
