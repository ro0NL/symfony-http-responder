<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class Respond
{
    /**
     * @var int
     */
    public $status = Response::HTTP_OK;

    /**
     * @var string[]|string[][]
     */
    public $headers = [];

    /**
     * @var string[]|string[][]
     */
    public $flashes = [];

    public function withStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string[]|string[][] $headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string|string[] $value
     */
    public function withHeader(string $name, $value): self
    {
        $this->headers[$name] = (array) $value;

        return $this;
    }

    /**
     * @param string[]|string[][] $flashes
     */
    public function withFlashes(array $flashes): self
    {
        $this->flashes = $flashes;

        return $this;
    }
}
