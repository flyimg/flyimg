<?php

namespace Tests\Core\StorageProvider;

use League\Flysystem\UnableToWriteFile;
use Core\Exception\MissingParamsException;
use Core\Handler\ImageHandler;
use Core\StorageProvider\S3StorageProvider;
use Tests\Core\BaseTest;

/**
 * Class S3StorageProviderTest
 */
class S3StorageProviderTest extends BaseTest
{
    /**
     * rf_1 regenerates via overwrite (write), so unreachable S3 fails on PutObject.
     */
    public function testUploadActionWithS3StorageS3Exception()
    {
        $this->expectException(UnableToWriteFile::class);

        unset($this->app['flysystems']);
        unset($this->app['image.handler']);
        $this->app['params']->addParameter(
            'aws_s3',
            [
                'access_id' => 'xxxxx',
                'secret_key' => 'xxxxx',
                'region' => 'xxxxx',
                'bucket_name' => 'xxxxx',
            ]
        );

        $this->app->register(new S3StorageProvider());
        /** Core Manager Service */
        $this->imageHandler =
            new ImageHandler(
                $this->app['flysystems']['storage_handler'],
                $this->app['params']
            );
        $this->imageHandler->processImage(parent::OPTION_URL . ',o_webp', parent::PNG_TEST_IMAGE);
    }

    /**
     *
     */
    public function testUploadActionWithS3()
    {
        unset($this->app['flysystems']);
        unset($this->app['image.handler']);
        $this->app['params']->addParameter(
            'aws_s3',
            [
                'access_id' => 'xxxxx',
                'secret_key' => 'xxxxx',
                'region' => 'xxxxx',
                'bucket_name' => 'xxxxx',
                'path_prefix' => 'xxxxx',
                'visibility' => 'PRIVATE',
            ]
        );

        $this->app->register(new S3StorageProvider());
        $this->assertStringMatchesFormat(
            'https://%s.s3.%s.amazonaws.com/%s',
            $this->app['flysystems']['file_path_resolver']
        );
    }

    /**
     *
     */
    public function testUploadActionWithS3StorageException()
    {
        $this->expectException(MissingParamsException::class);
        $this->app['params']->addParameter(
            'aws_s3',
            [
                'access_id' => 'xxxxx',
                'secret_key' => 'xxxxx',
                'region' => '', // region is required
                'bucket_name' => '' // bucket_name is required
            ]
        );
        $this->app->register(new S3StorageProvider());
    }

    /**
     * Test IRSA configuration without access_id and secret_key
     */
    public function testUploadActionWithS3IRSA()
    {
        unset($this->app['flysystems']);
        unset($this->app['image.handler']);
        $this->app['params']->addParameter(
            'aws_s3',
            [
                // access_id and secret_key are not needed with IRSA
                'region' => 'eu-central-1',
                'bucket_name' => 'test-bucket',
                'path_prefix' => 'test-prefix',
                'visibility' => 'PRIVATE',
            ]
        );

        $this->app->register(new S3StorageProvider());
        $this->assertStringMatchesFormat(
            'https://%s.s3.%s.amazonaws.com/%s',
            $this->app['flysystems']['file_path_resolver']
        );
    }
}
