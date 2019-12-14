<?php

declare(strict_types=1);

namespace Vendor\Package\Tests;

use PHPUnit\Framework\TestCase;
use Vendor\Package\Hello;

/**
 * @internal
 */
final class HelloTest extends TestCase
{
    public function testHello(): void
    {
        self::assertSame('Hello PHP', (new Hello())('PHP'));
    }
}
