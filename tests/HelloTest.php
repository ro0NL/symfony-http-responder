<?php

declare(strict_types=1);

namespace Vendor\Package\Tests;

use PHPUnit\Framework\TestCase;
use Vendor\Package\Hello;

final class HelloTest extends TestCase
{
    public function testHello(): void
    {
        $hello = new Hello();

        self::assertSame('Hello PHP', $hello('PHP'));
    }
}
