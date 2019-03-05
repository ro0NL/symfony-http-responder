<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Routing;

use ro0NL\HttpResponder\Respond;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RespondRouteRedirect extends Respond
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var int
     */
    public $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;

    public function __construct(string $name, array $parameters = [])
    {
        $this->status = Response::HTTP_FOUND;
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function withAbsoluteUrl(): self
    {
        $respond = clone $this;
        $respond->referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        return $respond;
    }

    public function withRelativePath(): self
    {
        $respond = clone $this;
        $respond->referenceType = UrlGeneratorInterface::RELATIVE_PATH;

        return $respond;
    }

    public function withNetworkPath(): self
    {
        $respond = clone $this;
        $respond->referenceType = UrlGeneratorInterface::NETWORK_PATH;

        return $respond;
    }
}
