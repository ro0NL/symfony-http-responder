<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bridge\Routing;

use ro0NL\HttpResponder\Respond\AbstractRespond;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class RouteRedirect extends AbstractRespond
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
        $this->name = $name;
        $this->parameters = $parameters;
        $this->status[0] = Response::HTTP_FOUND;
    }

    /**
     * @return $this
     */
    public function withAbsoluteUrl(): self
    {
        $this->referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        return $this;
    }

    /**
     * @return $this
     */
    public function withRelativePath(): self
    {
        $this->referenceType = UrlGeneratorInterface::RELATIVE_PATH;

        return $this;
    }

    /**
     * @return $this
     */
    public function withNetworkPath(): self
    {
        $this->referenceType = UrlGeneratorInterface::NETWORK_PATH;

        return $this;
    }
}
