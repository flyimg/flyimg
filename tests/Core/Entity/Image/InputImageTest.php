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
}
