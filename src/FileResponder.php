<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder;

use ro0NL\HttpResponder\Respond\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class FileResponder extends ProvidingResponder
{
    protected function getProviders(): iterable
    {
        yield File::class => function (File $respond): BinaryFileResponse {
            $public = true;
            $autoEtag = File::USE_ETAG === (File::USE_ETAG & $respond->mode);
            $autoLastModified = File::USE_MODIFICATION_DATE === (File::USE_MODIFICATION_DATE & $respond->mode);
            $response = new BinaryFileResponse($respond->file, $respond->status, $respond->headers, $public, null, $autoEtag, $autoLastModified);

            if (null !== $respond->contentDisposition) {
                [$disposition, $filename, $filenameFallback] = $respond->contentDisposition;

                $response->setContentDisposition($disposition, $filename, $filenameFallback);
            }

            return $response;
        };
    }
}
