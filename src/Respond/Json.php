<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Json extends AbstractRespond
{
    /** @var mixed */
    public $data;
    /** @var int|null */
    public $encodingOptions;
    /** @var string|null */
    public $callback;
    /** @var bool */
    public $raw = false;

    /**
     * @param mixed $data
     */
    public function __construct($data, int $encodingOptions = null)
    {
        $this->data = $data;
        $this->encodingOptions = $encodingOptions;
    }

    public static function raw(string $data, int $encodingOptions = null): self
    {
        $respond = new self($data, $encodingOptions);
        $respond->raw = true;

        return $respond;
    }

    /**
     * @return $this
     */
    public function withCallback(string $callback): self
    {
        $this->callback = $callback;

        return $this;
    }
}
