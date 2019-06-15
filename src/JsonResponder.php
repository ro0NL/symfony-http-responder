<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Respond\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class JsonResponder extends ProvidingResponder
{
    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function getProviders(): iterable
    {
        yield Json::class => static function (Json $respond): JsonResponse {
            if ($respond->raw) {
                if (!\is_string($respond->data)) {
                    throw new \LogicException(sprintf('JSON must be a string, got "%s".', \gettype($respond->data)));
                }

                /** @var JsonResponse $response */
                $response = JsonResponse::fromJsonString($respond->data);
            } else {
                $response = new JsonResponse($respond->data);
            }

            if (null !== $respond->encodingOptions) {
                $response->setEncodingOptions($respond->encodingOptions);
            }

            if (null !== $respond->callback) {
                $response->setCallback($respond->callback);
            }

            return $response;
        };
    }
}
