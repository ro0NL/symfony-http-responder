<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Symfony\Component\HttpFoundation\Response;

/**
 * A first class HTTP respond type.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
abstract class AbstractRespond implements Respond
{
    /** @var array{0: int, 1: string|null} */
    public $status = [Response::HTTP_OK, null];

    /** @var null|\DateTimeInterface */
    public $date;

    /** @var array<string, string[]> */
    public $headers = [];

    /** @var array<string, scalar[]> */
    public $flashes = [];

    /** @var null|GenericLinkProvider */
    public $linkProvider;

    /**
     * @return $this
     */
    public function withStatus(int $code, ?string $text = null): self
    {
        $this->status = [$code, $text];

        return $this;
    }

    /**
     * @return $this
     */
    public function withDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @param array<string, string|string[]> $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = [];

        foreach ($headers as $name => $values) {
            $this->withHeader($name, $values);
        }

        return $this;
    }

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function withHeader(string $name, $value): self
    {
        $this->headers[$name] = (array) $value;

        return $this;
    }

    /**
     * @param array<string, scalar|scalar[]> $flashes
     *
     * @return $this
     */
    public function withFlashes(array $flashes): self
    {
        $this->flashes = [];

        foreach ($flashes as $type => $messages) {
            $this->withFlash($type, $messages);
        }

        return $this;
    }

    /**
     * @param scalar|scalar[] $message
     *
     * @return $this
     */
    public function withFlash(string $type, $message): self
    {
        if (\is_array($message)) {
            $this->flashes[$type] = array_merge($this->flashes[$type] ?? [], $message);
        } else {
            $this->flashes[$type][] = $message;
        }

        return $this;
    }

    /**
     * @param string[]        $rels
     * @param (string|bool)[] $attributes
     *
     * @return $this
     */
    public function withLink(string $href, array $rels = [], array $attributes = []): self
    {
        if (!class_exists(Link::class)) {
            throw new \LogicException(sprintf('Respond "%s" cannot have links because no link provider is available. Try running "composer require fig/link-util".', \get_class($this)));
        }

        $link = new Link(array_shift($rels) ?? '', $href);
        foreach ($rels as $rel) {
            $link = $link->withRel($rel);
        }
        foreach ($attributes as $attribute => $value) {
            if (false === $value) {
                continue;
            }
            if (true === $value) {
                $attribute = $value = (string) $attribute;
            } elseif (\is_int($attribute)) {
                $attribute = $value = (string) $value;
            }
            $link = $link->withAttribute($attribute, $value);
        }

        if (null === $this->linkProvider) {
            $this->linkProvider = new GenericLinkProvider([$link]);
        } else {
            $this->linkProvider = $this->linkProvider->withLink($link);
        }

        return $this;
    }
}
