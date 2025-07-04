<?php

namespace Tests\Core\Entity\Image;

use Core\Entity\Image\InputImage;
use Core\Entity\OptionsBag;
use Core\Exception\ReadFileException;
use Tests\Core\BaseTest;

#[BackupGlobals('disabled')]
class InputImageTest extends BaseTest
{
    /**
     * Test SaveToTemporaryFileException
     */
    public function testSaveToTemporaryFileException()
    {
        $this->expectException(ReadFileException::class);
        $this->expectExceptionMessage('File ' . parent::JPG_TEST_IMAGE . '--fail does not exist.');
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), 'o_jpg');

        new InputImage($optionsBag, parent::JPG_TEST_IMAGE . '--fail');
    }
}
