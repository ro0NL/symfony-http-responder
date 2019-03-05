<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

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
        $respond = clone $this;
        $respond->status = $status;

        return $respond;
    }

    /**
     * @param string[]|string[][] $headers
     */
    public function withHeaders(array $headers): self
    {
        $respond = clone $this;
        $respond->headers = $headers;

        return $respond;
    }

    /**
     * @param string[]|string[][] $flashes
     */
    public function withFlashes(array $flashes): self
    {
        $respond = clone $this;
        $respond->flashes = $flashes;

        return $respond;
    }
}