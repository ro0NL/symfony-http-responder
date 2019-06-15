<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Respond;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class File extends AbstractRespond
{
    public const USE_NONE = 0;
    public const USE_ALL = self::USE_ETAG | self::USE_MODIFICATION_DATE;
    public const USE_ETAG = 1;
    public const USE_MODIFICATION_DATE = 2;

    /**
     * @var \SplFileInfo|string
     */
    public $file;

    /**
     * @var int
     */
    public $mode;

    /**
     * @var array{0: string, 1: string, 2: string}|null
     */
    public $contentDisposition;

    /**
     * @param \SplFileInfo|string $file
     */
    public function __construct($file, int $mode = self::USE_MODIFICATION_DATE)
    {
        $this->file = $file;
        $this->mode = $mode;
    }

    /**
     * @return $this
     */
    public function asInlineContent(string $filename = '', string $filenameFallback = ''): self
    {
        $this->contentDisposition = [ResponseHeaderBag::DISPOSITION_INLINE, $filename, $filenameFallback];

        return $this;
    }

    /**
     * @return $this
     */
    public function asDownload(string $filename = '', string $filenameFallback = ''): self
    {
        $this->contentDisposition = [ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename, $filenameFallback];

        return $this;
    }
}
