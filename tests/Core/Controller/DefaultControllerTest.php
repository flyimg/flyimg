<?php

namespace Tests\Core\Controller;

use Core\Exception\ExecFailedException;
use Core\Exception\InvalidArgumentException;
use Core\Exception\AppException;
use Core\Exception\ReadFileException;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tests\Core\BaseTest;

/**
 * Class DefaultControllerTest
 *
 * @category Tests
 * @package  Flyimg\\Tests
 * @author   Flyimg Team <dev@flyimg.io>
 * @license  MIT License <https://opensource.org/licenses/MIT>
 * @link     https://github.com/flyimg/flyimg
 */
class DefaultControllerTest extends BaseTest
{
    protected function tearDown(): void
    {
        unset($this->app);
    }

    public function testIndexAction()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testIndexActionWithDemoDisabled()
    {
        $this->app['params']->addParameter('demo_page_enabled', false);
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testUploadAction()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,c_1,rf_1,o_png/' . BaseTest::JPG_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadActionWebp()
    {
        $client = static::createClient();
        $client->request('GET', 'upload/o_webp/' . BaseTest::PNG_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadActionAvif()
    {
        $client = static::createClient();
        $client->request('GET', 'upload/o_avif/' . BaseTest::AVIF_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadActionGif()
    {
        $client = static::createClient();
        $client->request('GET', 'upload/o_gif/' . BaseTest::PNG_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    /** Ensure POST /upload with octet-stream body works. */
    public function testUploadPostOctetStream()
    {
        $client = static::createClient();
        $binary = file_get_contents(BaseTest::PNG_TEST_IMAGE);
        $client->request(
            'POST',
            '/upload/w_50,o_jpg',
            [],
            [],
            ['CONTENT_TYPE' => 'application/octet-stream'],
            $binary
        );
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    /** Ensure POST /upload with JSON {base64} works. */
    public function testUploadPostJsonBase64()
    {
        $client = static::createClient();
        $binary = file_get_contents(BaseTest::JPG_TEST_IMAGE);
        $payload = json_encode(['base64' => base64_encode($binary)]);
        $client->request(
            'POST',
            '/upload/w_60,o_png',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    /** Ensure GET /upload with data URI works. */
    public function testUploadGetDataUri()
    {
        $client = static::createClient();
        $binary = file_get_contents(BaseTest::PNG_TEST_IMAGE);
        $dataUri = 'data:image/png;base64,' . base64_encode($binary);
        $client->request('GET', '/upload/w_40,o_jpg/' . $dataUri);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    /** Ensure s3:// without configuration throws AppException. */
    public function testUploadS3WithoutConfig()
    {
        $this->expectException(AppException::class);
        $client = static::createClient();
        $client->request('GET', '/upload/w_20/' . 's3://nonexistent-bucket/path/to/image.png');
    }

    public function testUploadActionWithFaceDetection()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/fc_1/' . BaseTest::FACES_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadActionForbidden()
    {
        $this->expectException(ReadFileException::class);
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,c_1/Rovinj-Croatia-nonExist.jpg');
    }

    public function testUploadActionInvalidExtension()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,c_1,o_xxx/' . BaseTest::JPG_TEST_IMAGE);
    }

    public function testPathAction()
    {
        $client = static::createClient();
        $client->request('GET', '/path/w_200,h_200,c_1/' . BaseTest::JPG_TEST_IMAGE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testPathActionForbidden()
    {
        $this->expectException(ReadFileException::class);
        $client = static::createClient();
        $client->request('GET', '/path/w_200,h_200,c_1/Rovinj-Croatia-nonExist.jpg');
    }

    public function testUploadPdfNoPageSpecified()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200/' . BaseTest::PDF_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadPdfPage2()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,pg_2/' . BaseTest::PDF_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadPdfWithDensity()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,dnst_300/' . BaseTest::PDF_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadMovieNoTimeSpecified()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200/' . BaseTest::MOVIE_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadMovie5Seconds()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,tm_5/' . BaseTest::MOVIE_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadMovie10Seconds()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,tm_00:00:10/' . BaseTest::MOVIE_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadMovie10SecondsRefresh()
    {
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,tm_00:00:10,rf_1/' . BaseTest::MOVIE_TEST_FILE);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isEmpty());
    }

    public function testUploadMovie20SecondsFails()
    {
        $this->expectException(ExecFailedException::class);
        $client = static::createClient();
        $client->request('GET', '/upload/w_200,h_200,tm_00:00:20/' . BaseTest::MOVIE_TEST_FILE);
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = include __DIR__ . '/../../../app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }
}
