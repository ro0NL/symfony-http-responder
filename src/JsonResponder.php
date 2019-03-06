<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Exception\BadRespondTypeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class JsonResponder implements Responder
{
    public function respond(Respond $respond): Response
    {
        if (!$respond instanceof RespondJson) {
            throw BadRespondTypeException::create($this, $respond);
        }

        if ($respond->raw && !\is_string($respond->data)) {
            throw new \LogicException(sprintf('JSON must be a string, got "%s".', \gettype($respond->data)));
        }

        $response = new JsonResponse($respond->data, $respond->status, $respond->headers, $respond->raw);

        if (null !== $respond->encodingOptions) {
            $response->setEncodingOptions($respond->encodingOptions);
        }

        if (null !== $respond->callback) {
            $response->setCallback($respond->callback);
        }

        return $response;
    }
}
