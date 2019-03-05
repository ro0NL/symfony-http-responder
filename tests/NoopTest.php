<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use PHPUnit\Framework\TestCase;

final class NoopTest extends TestCase
{
    public function testNoop(): void
    {
        self::assertTrue(true);
    }
}
