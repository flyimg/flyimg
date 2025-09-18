<?php

namespace Tests\Core\Entity\Image;

use Core\Entity\Image\InputImage;
use Core\Entity\Image\OutputImage;
use Core\Entity\OptionsBag;
use Tests\Core\BaseTest;

#[BackupGlobals('disabled')]
class OutputImageTest extends BaseTest
{
    /**
     * Test parseOptions Method
     */
    public function testParseOptions()
    {
        /**
         * This array needs to be updated every time we
         * add or remove a URL option in the config/parametesr.yml file.
         */
        $expectedParseArray = [
            'quality' => 90,
            'output' => 'auto',
            'unsharp' => '0.25x0.25+8+0.065',
            'sharpen' => null,
            'blur' => null,
            'width' => '200',
            'height' => '100',
            'face-crop' => 0,
            'face-crop-position' => 0,
            'face-blur' => '1',
            'crop' => '1',
            'background' => '#999999',
            'strip' => 1,
            'auto-orient' => 0,
            'resize' => '1',
            'gravity' => 'Center',
            'filter' => 'Lanczos',
            'rotate' => '-45',
            'text' => null,
            'text-color' => 'white',
            'text-size' => 14,
            'text-bg' => null,
            'scale' => '50',
            'sampling-factor' => '1x1',
            'refresh' => '1',
            'smart-crop' => false,
            'extent' => '100x80',
            'preserve-aspect-ratio' => true,
            'preserve-natural-size' => true,
            'webp-lossless' => false,
            'webp-method' => 4,
            'gif-frame' => 0,
            'extract' => null,
            'extract-top-x' => null,
            'extract-top-y' => null,
            'extract-bottom-x' => null,
            'extract-bottom-y' => null,
            'pdf-page-number' => 1,
            'density' => 72,
            'time' => '00:00:01',
            'colorspace' =>  'sRGB',
            'monochrome' => null,
            'version' => null,
        ];
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), self::OPTION_URL);
        $inputImage = new InputImage($optionsBag, self::JPG_TEST_IMAGE);

        $this->assertEquals($inputImage->optionsBag()->asArray(), $expectedParseArray);
    }

    /**
     * Test SaveToTemporaryFile
     */
    public function testSaveToTemporaryFile()
    {
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), self::OPTION_URL);
        $inputImage = new InputImage($optionsBag, self::JPG_TEST_IMAGE);
        $image = new OutputImage($inputImage);
        $this->generatedImage[] = $image;

        $this->assertFileExists($image->getInputImage()->sourceImagePath());
    }

    /**
     * Test GenerateFilesName
     */
    public function testGenerateFilesName()
    {
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), self::OPTION_URL);
        $inputImage = new InputImage($optionsBag, self::JPG_TEST_IMAGE);
        $image = new OutputImage($inputImage);

        $optionsBag2 = new OptionsBag($this->imageHandler->appParameters(), self::OPTION_URL);
        $inputImage2 = new InputImage($optionsBag2, self::JPG_TEST_IMAGE);
        $image2 = new OutputImage($inputImage2);

        $this->generatedImage[] = $image2;
        $this->generatedImage[] = $image;

        $this->assertEquals($image2->getOutputImageName(), $image->getOutputImageName());
        $this->assertNotEquals($image2->getOutputTmpPath(), $image->getOutputTmpPath());
    }

    /**
     * Test ExtractByKey
     */
    public function testExtractByKey()
    {
        $optionsBag = new OptionsBag($this->imageHandler->appParameters(), self::OPTION_URL);
        $inputImage = new InputImage($optionsBag, self::JPG_TEST_IMAGE);
        $image = new OutputImage($inputImage);

        $image->extractKey('width');
        $this->generatedImage[] = $image;
        $this->assertFalse(array_key_exists('width', $image->getInputImage()->optionsBag()->asArray()));
    }
}
