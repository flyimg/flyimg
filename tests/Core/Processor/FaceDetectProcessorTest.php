<?php

namespace Tests\Core\Processor;

use Tests\Core\BaseTest;

/**
 * Class FaceDetectProcessorTest
 * @package Tests\Core\Processor
 */
class FaceDetectProcessorTest extends BaseTest
{
    /**
     * @throws \Exception
     */
    public function testProcessFaceCropping()
    {
        $image = $this->imageHandler->processImage('fc_1,o_png,rf_1', parent::FACES_TEST_IMAGE);
        $filesize = filesize($image->getOutputTmpPath());
        $filesize2 = filesize(parent::FACES_CP0_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals($filesize, $filesize2);
    }

     /**
     * @throws \Exception
     */
    public function testProcessFaceCroppingAvifOutput()
    {
        $image = $this->imageHandler->processImage('fc_1,o_avif,rf_1', parent::FACES_TEST_IMAGE);
        $filesize = filesize($image->getOutputTmpPath());
        $filesize2 = filesize(parent::FACES_CP0_TEST_IMAGE_AVIF);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals($filesize, $filesize2);
    }

    /**
     * @throws \Exception
     */
    public function testProcessFaceBlurring()
    {
        $image = $this->imageHandler->processImage('fb_1,o_png,rf_1', parent::FACES_TEST_IMAGE);
        $filesize = filesize($image->getOutputTmpPath());
        $filesize2 = filesize(parent::FACES_BLUR_TEST_IMAGE);
        // $image1Crc32 = crc32(\file_get_contents($image->getOutputImagePath()));
        // $image2Crc32 = crc32(\file_get_contents(parent::FACES_BLUR_TEST_IMAGE));
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals($filesize, $filesize2);
    }
}
