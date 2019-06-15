<?php

declare(strict_types=1);

namespace Vendor\Package;

/**
 * @psalm-suppress UnusedClass
 */
final class Hello
{
    public function __invoke(string $name): string
    {
        return 'Hello '.$name;
    }
}
