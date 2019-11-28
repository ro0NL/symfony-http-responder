<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Tests;

use ro0NL\HttpResponder\FileResponder;
use ro0NL\HttpResponder\Respond\File;
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Test\ResponderTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

final class FileResponderTest extends ResponderTestCase
{
    protected const DEFAULT_RESPONSE_CLASS = BinaryFileResponse::class;

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFile($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond(new File($file));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertArrayHasKey('Last-Modified', $headers);
        self::assertArrayNotHasKey('ETag', $headers);
    }

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFileWithEtag($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond(new File($file, File::USE_ETAG));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertArrayHasKey('ETag', $headers);
        self::assertArrayNotHasKey('Last-Modified', $headers);
    }

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFileWithAllAttributes($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond(new File($file, File::USE_ALL));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertArrayHasKey('ETag', $headers);
        self::assertArrayHasKey('Last-Modified', $headers);
    }

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFileWithNoAttributes($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond(new File($file, File::USE_NONE));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertArrayNotHasKey('ETag', $headers);
        self::assertArrayNotHasKey('Last-Modified', $headers);
    }

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFileAsInlineContent($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond((new File($file))->asInlineContent('filename', 'fall back'));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertSame('inline; filename="fall back"; filename*=utf-8\'\'filename', $headers['Content-Disposition'][0] ?? null);
    }

    /**
     * @dataProvider provideFiles
     *
     * @param string|\SplFileInfo $file
     */
    public function testRespondFileAsDownload($file): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->doRespond((new File($file))->asDownload('filename', 'fall back'));
        $headers = $response->headers->allPreserveCase();

        self::assertResponse($response);
        self::assertSame((string) $file, $response->getFile()->getPathname());
        self::assertSame('attachment; filename="fall back"; filename*=utf-8\'\'filename', $headers['Content-Disposition'][0] ?? null);
    }

    public function provideFiles(): iterable
    {
        yield [__FILE__];
        //yield [new \SplFileInfo(__FILE__)];
    }

    public function testRespondWithInvalidFile(): void
    {
        $responder = $this->getResponder();

        $this->expectException(FileNotFoundException::class);

        $responder->respond(new File('/does-not-exists'));
    }

    protected function getResponder(): Responder
    {
        return new FileResponder();
    }

    /**
     * @psalm-suppress InvalidReturnType
     */
    protected function getResponds(): iterable
    {
        yield new File(__FILE__);
        yield new File(new \SplFileInfo(__FILE__));
    }
}
