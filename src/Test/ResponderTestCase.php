<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Test;

use PHPUnit\Framework\TestCase;
use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use ro0NL\HttpResponder\Respond;
use ro0NL\HttpResponder\Responder;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class ResponderTestCase extends TestCase
{
    public function testUnknownRespond(): void
    {
        $responder = $this->getResponder();

        $this->expectException(BadRespondTypeException::class);

        $responder->respond($this->getMockForAbstractClass(Respond::class));
    }

    abstract protected function getResponder(): Responder;
}
