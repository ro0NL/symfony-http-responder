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
        yield File::class => static function (File $respond): BinaryFileResponse {
            $response = new BinaryFileResponse($respond->file, 200, [], true, null, false, false);

            if (File::USE_ETAG === (File::USE_ETAG & $respond->mode)) {
                $response->setAutoEtag();
            }

            if (File::USE_MODIFICATION_DATE === (File::USE_MODIFICATION_DATE & $respond->mode)) {
                $response->setAutoLastModified();
            }

            if (null !== $respond->contentDisposition) {
                [$disposition, $filename, $filenameFallback] = $respond->contentDisposition;

                $response->setContentDisposition($disposition, $filename, $filenameFallback);
            }

            return $response;
        };
    }
}
