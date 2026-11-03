<?php

namespace Tests\Core\Entity\Image;

use Core\Entity\Image\InputImage;
use Core\Entity\OptionsBag;
use Core\Exception\FileNotFoundException;
use Core\Exception\InvalidArgumentException;
use Tests\Core\BaseTest;

#[BackupGlobals('disabled')]
class InputImageTest extends BaseTest
{
    /**
     * Test SaveToTemporaryFileException
     */
    public function testSaveToTemporaryFileException()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File ' . parent::JPG_TEST_IMAGE . '--fail does not exist.');
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');

        new InputImage($optionsBag, parent::JPG_TEST_IMAGE . '--fail');
    }

    /**
     * An empty source (e.g. GET /upload/w_100 or /upload/w_100/) is a client error.
     */
    public function testEmptySourceThrowsInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');

        new InputImage($optionsBag, '');
    }

    /**
     * A source that is neither a URL, a data URI, an s3 URI nor an existing file.
     */
    public function testUnknownSourceThrowsFileNotFound()
    {
        $this->expectException(FileNotFoundException::class);
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');

        new InputImage($optionsBag, 'not-a-url');
    }

    /**
     * Atomic write replaces the shared original path completely and leaves no .part file.
     *
     * @return void
     */
    public function testWriteSourceImageAtomicallyReplacesSharedPath()
    {
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');
        $inputImage = new InputImage($optionsBag, parent::JPG_TEST_IMAGE);
        $sourcePath = $inputImage->sourceImagePath();

        $this->assertFileExists($sourcePath);
        $this->assertSame(
            file_get_contents(parent::JPG_TEST_IMAGE),
            file_get_contents($sourcePath)
        );

        $replacement = file_get_contents(parent::PNG_TEST_IMAGE);
        $method = new \ReflectionMethod(
            InputImage::class,
            'writeSourceImageAtomically'
        );
        $method->invoke($inputImage, $replacement);

        $this->assertFileExists($sourcePath);
        $this->assertSame($replacement, file_get_contents($sourcePath));
        $this->assertSame([], glob($sourcePath . '.*.part') ?: []);

        @unlink($sourcePath);
        @unlink($sourcePath . '.lock');
    }

    /**
     * Refresh re-copies the local source via the atomic write path.
     *
     * @return void
     */
    public function testRefreshRewritesSourceViaAtomicWrite()
    {
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');
        $first = new InputImage($optionsBag, parent::PNG_TEST_IMAGE);
        $sourcePath = $first->sourceImagePath();
        $this->assertFileExists($sourcePath);

        file_put_contents($sourcePath, 'stale-partial-content');

        $refreshBag = new OptionsBag(
            $this->imageHandler->appParameters(),
            'o_jpg,rf_1'
        );
        $refreshed = new InputImage($refreshBag, parent::PNG_TEST_IMAGE);

        $this->assertSame($sourcePath, $refreshed->sourceImagePath());
        $this->assertSame(
            file_get_contents(parent::PNG_TEST_IMAGE),
            file_get_contents($sourcePath)
        );
        $this->assertSame([], glob($sourcePath . '.*.part') ?: []);

        @unlink($sourcePath);
        @unlink($sourcePath . '.lock');
    }
}
