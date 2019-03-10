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
     * @var array
     */
    public $status = [Response::HTTP_OK, null];

    /**
     * @var string[]|string[][]
     */
    public $headers = [];

    /**
     * @var array
     */
    public $flashes = [];

    public function withStatus(int $code, string $text = null): self
    {
        $this->status = [$code, $text];

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

    /**
     * @param mixed $message
     */
    public function withFlash(string $type, $message): self
    {
        $this->flashes[$type][] = $message;

        return $this;
    }
}
