<?php

namespace Tests\Core\Service;

use Core\Entity\Image\InputImage;
use Core\Handler\ImageHandler;
use Core\Processor\Processor;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Tests\Core\BaseTest;

/**
 * Class ImageHandlerTest
 */
class ImageHandlerTest extends BaseTest
{
    /**
     * rf_1 must regenerate by overwriting storage, never delete-first.
     *
     * @return void
     */
    public function testRefreshOverwritesWithoutDeletingFirst()
    {
        $options = 'w_50,h_50,o_jpg';
        $first = $this->imageHandler->processImage($options, parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $first;
        $outputName = $first->getOutputImageName();
        $storage = $this->app['flysystems']['storage_handler'];
        $this->assertTrue($storage->has($outputName));

        $deleteCalled = false;
        $spy = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'delete', 'write', 'read'])
            ->getMock();
        $spy->method('has')->willReturnCallback(
            static function (string $path) use ($storage) {
                return $storage->has($path);
            }
        );
        $spy->method('delete')->willReturnCallback(
            static function (string $path) use ($storage, &$deleteCalled): void {
                $deleteCalled = true;
                $storage->delete($path);
            }
        );
        $spy->method('write')->willReturnCallback(
            static function (string $path, string $contents) use ($storage): void {
                $storage->write($path, $contents);
            }
        );
        $spy->method('read')->willReturnCallback(
            static function (string $path) use ($storage) {
                return $storage->read($path);
            }
        );

        $handler = new ImageHandler($spy, $this->app['params']);
        $refreshed = $handler->processImage($options . ',rf_1', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $refreshed;

        $this->assertFalse(
            $deleteCalled,
            'refresh must not delete the stored output before regenerating'
        );
        $this->assertTrue($storage->has($outputName));
        $this->assertSame($outputName, $refreshed->getOutputImageName());
        $this->assertNotEmpty($refreshed->getOutputImageContent());
    }

    /**
     */
    public function testProcessPNG()
    {
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL, parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::JPEG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessWebpFromPng()
    {
        $image = $this->imageHandler->processImage(parent::OPTION_URL . ',o_webp', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::WEBP_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
        $this->assertWebpEncoder($image->getCommandString(), '4');
    }

    /**
     * Same shape as /upload/q_80,o_webp/<jpeg>: cwebp encodes, and a hostile
     * webpm value must not reach the shell.
     */
    public function testWebpQualityCommandIsSafe()
    {
        $image = $this->imageHandler->processImage('q_80,o_webp,rf_1', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::WEBP_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
        $this->assertWebpEncoder($image->getCommandString(), '4');

        $hostile = $this->imageHandler->processImage('q_80,o_webp,webpm_4;id,rf_1', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $hostile;
        $command = $hostile->getCommandString();
        $this->assertWebpEncoder($command, '4');
        $this->assertStringNotContainsString(';id', $command);
        $this->assertEquals(InputImage::WEBP_MIME_TYPE, $this->getFileMimeType($hostile->getOutputTmpPath()));
    }

    /**
     * @param string $command
     * @param string $method
     */
    private function assertWebpEncoder(string $command, string $method): void
    {
        if (!is_executable(Processor::CWEBP_COMMAND)) {
            return;
        }

        $this->assertStringContainsString("'" . Processor::CWEBP_COMMAND . "'", $command);
        $this->assertStringContainsString("-m '" . $method . "'", $command);
        $this->assertStringNotContainsString('webp:thread-level', $command);
        $this->assertStringNotContainsString('webp:method', $command);
        $this->assertStringNotContainsString(';id', $command);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessAvifFromPng()
    {
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_avif', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::AVIF_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessAutoFromPngToGenerateAvif()
    {
        $_SERVER['HTTP_ACCEPT'] = 'image/avif';
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_auto', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::AVIF_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessAutoFromPngToGenerateWebp()
    {
        $_SERVER['HTTP_ACCEPT'] = 'image/webp';
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_auto', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::WEBP_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessInputFromPng()
    {
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_input', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::PNG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessInputFromJpeg()
    {
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_input', parent::JPG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::JPEG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProcessInputFromGif()
    {
        $image = $this->imageHandler->processImage(parent::CROP_OPTION_URL . ',o_input', parent::GIF_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::GIF_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessJpgFromPng()
    {
        $image = $this->imageHandler->processImage(parent::OPTION_URL . ',o_jpg', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::JPEG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessGifFromPng()
    {
        $image = $this->imageHandler->processImage(parent::OPTION_URL . ',o_gif', parent::PNG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::GIF_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessJpg()
    {
        $image = $this->imageHandler->processImage(parent::OPTION_URL, parent::JPG_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
    }

    /**
     */
    public function testProcessGif()
    {
        $image = $this->imageHandler->processImage(parent::GIF_OPTION_URL, parent::GIF_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::GIF_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessPngFromGif()
    {
        $image = $this->imageHandler->processImage(parent::GIF_OPTION_URL . ',o_png', parent::GIF_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::PNG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessJpgFromGif()
    {
        $image = $this->imageHandler->processImage(parent::GIF_OPTION_URL . ',o_jpg', parent::GIF_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::JPEG_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     */
    public function testProcessWebpFromGif()
    {
        $image = $this->imageHandler->processImage(parent::GIF_OPTION_URL . ',o_webp', parent::GIF_TEST_IMAGE);
        $this->generatedImage[] = $image;
        $this->assertFileExists($image->getOutputTmpPath());
        $this->assertEquals(InputImage::WEBP_MIME_TYPE, $this->getFileMimeType($image->getOutputTmpPath()));
    }

    /**
     * @param $filePath
     *
     * @return mixed
     */
    protected function getFileMimeType($filePath)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
    }
}
